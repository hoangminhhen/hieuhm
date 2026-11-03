<?php require_once 'views/admin/layout/header.php'; ?>

<div class="card shadow mb-4">
    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
        <span><i class="fas fa-warehouse me-2"></i> Nhập Kho Sản Phẩm (Nhiều Size)</span>
    </div>
    <div class="card-body">
        <form action="index.php?controller=adminStock&action=store" method="POST" id="importForm">
            <?= CsrfHelper::csrfField() ?>
            
            <div class="mb-4 p-3 bg-light border rounded">
                <label class="fw-bold mb-2">Bước 1: Chọn Thương hiệu</label>
                <select id="brandSelect" class="form-select w-50" onchange="loadProducts()">
                    <option value="">-- Chọn thương hiệu --</option>
                    <?php foreach($brands as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label class="fw-bold mb-2">Bước 2: Chi tiết đơn nhập</label>
            
            <div id="product-container"></div>

            <div class="mt-3 text-center p-4 border-top border-2 border-light dashed">
                <button type="button" class="btn btn-outline-primary btn-lg dashed-btn" onclick="addProductBlock()">
                    <i class="fas fa-plus-circle me-2"></i> Thêm Sản Phẩm Khác
                </button>
            </div>

            <div class="d-flex justify-content-end mt-4 sticky-bottom bg-white p-2 border-top">
                <button type="submit" class="btn btn-success fw-bold px-5 btn-lg shadow">
                    <i class="fas fa-save me-2"></i> LƯU KHO
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .product-block {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        position: relative;
        transition: all 0.3s;
    }
    .product-block:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #adb5bd;
    }
    .remove-block-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .variant-table th { font-size: 0.85rem; text-transform: uppercase; }
    .dashed-btn { border-style: dashed; border-width: 2px; }
</style>

<script>
    let currentProducts = [];

    // 1. Load sản phẩm khi chọn Brand
    function loadProducts() {
        const brandId = document.getElementById('brandSelect').value;
        const container = document.getElementById('product-container');
        container.innerHTML = ''; // Reset lại form

        if(!brandId) return;

        // Hiển thị loading...
        container.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Đang tải sản phẩm...</div>';

        fetch(`index.php?controller=adminStock&action=getProductsAjax&brand_id=${brandId}`)
            .then(res => res.json())
            .then(data => {
                currentProducts = data;
                container.innerHTML = ''; // Xóa loading
                
                if(currentProducts.length > 0) {
                    addProductBlock(); // Thêm sẵn 1 khối đầu tiên
                } else {
                    alert('Thương hiệu này chưa có sản phẩm nào!');
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div class="alert alert-danger">Lỗi tải dữ liệu!</div>';
            });
    }

    function addProductBlock() {
        if(currentProducts.length === 0) {
            alert('Vui lòng chọn Thương hiệu trước!');
            return;
        }

        const container = document.getElementById('product-container');
        const blockId = 'block-' + new Date().getTime(); 

        let options = '<option value="">-- Chọn sản phẩm --</option>';
        currentProducts.forEach(p => {
            options += `<option value="${p.id}">${p.name}</option>`;
        });

        const div = document.createElement('div');
        div.className = 'product-block fade-in';
        div.id = blockId;

        div.innerHTML = `
            <button type="button" class="btn btn-danger btn-sm remove-block-btn" onclick="removeBlock('${blockId}')" title="Xóa cả khối sản phẩm này">
                <i class="fas fa-times"></i>
            </button>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="fw-bold text-primary">Sản phẩm:</label>
                    <select class="form-select product-select" onchange="updateHiddenIds('${blockId}', this.value)" required>
                        ${options}
                    </select>
                </div>
            </div>

            <div class="bg-white p-2 rounded border">
                <table class="table table-sm table-borderless mb-0 variant-table">
                    <thead class="text-secondary border-bottom">
                        <tr>
                            <th width="20%">Size</th>
                            <th width="20%">Số lượng</th>
                            <th width="30%">Giá nhập (VNĐ)</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody id="tbody-${blockId}">
                        </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addVariantRow('${blockId}')">
                    <i class="fas fa-plus"></i> Thêm Size
                </button>
            </div>
        `;

        container.appendChild(div);

        addVariantRow(blockId);
    }

    function addVariantRow(blockId) {
        const tbody = document.getElementById(`tbody-${blockId}`);
 
        const block = document.getElementById(blockId);
        const selectedProductId = block.querySelector('.product-select').value;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="hidden" name="product_id[]" value="${selectedProductId}" class="hidden-pid">
                <input type="text" name="size[]" class="form-control form-control-sm" placeholder="Size" required>
            </td>
            <td>
                <input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="1" required>
            </td>
            <td>
                <input type="number" name="import_price[]" class="form-control form-control-sm" min="0" value="0">
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="removeVariantRow(this)">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    function updateHiddenIds(blockId, newProductId) {
        const block = document.getElementById(blockId);
        const hiddenInputs = block.querySelectorAll('.hidden-pid');
        hiddenInputs.forEach(input => {
            input.value = newProductId;
        });
    }
e
    function removeVariantRow(btn) {
        const tbody = btn.closest('tbody');

        if(tbody.children.length > 1) {
            btn.closest('tr').remove();
        } else {
            alert('Mỗi sản phẩm phải có ít nhất 1 dòng nhập!');
        }
    }

    function removeBlock(blockId) {
        const container = document.getElementById('product-container');

        if(container.children.length > 1) {
            document.getElementById(blockId).remove();
        } else {

            const block = document.getElementById(blockId);
            block.querySelector('select').value = "";
            const tbody = block.querySelector('tbody');
            tbody.innerHTML = "";
            addVariantRow(blockId);
        }
    }

    document.getElementById('importForm').addEventListener('submit', function(e) {
 
        const selects = document.querySelectorAll('.product-select');
        for(let select of selects) {
            if(!select.value) {
                e.preventDefault();
                alert('Vui lòng chọn tên sản phẩm cho tất cả các khối!');
                select.focus();
                return;
            }
        }
    });
</script>

<?php require_once 'views/admin/layout/footer.php'; ?>