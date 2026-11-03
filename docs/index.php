<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Kicks - Swagger / OpenAPI 3.0 Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f7fb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .topbar-ocean {
            background: linear-gradient(135deg, #07263e 0%, #0369a1 100%);
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.2);
        }
        .topbar-ocean h1 {
            margin: 0;
            font-size: 1.35rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .topbar-ocean a {
            color: #bae6fd;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 6px 14px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            transition: all 0.2s;
        }
        .topbar-ocean a:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        #swagger-ui {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .swagger-ui .topbar { display: none !important; }
        .swagger-ui .btn.authorize {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: white !important;
        }
        .swagger-ui .opblock.opblock-post {
            border-color: #0284c7;
            background: rgba(2, 132, 199, 0.05);
        }
        .swagger-ui .opblock.opblock-post .opblock-summary-method {
            background: #0284c7;
        }
    </style>
</head>
<body>
    <div class="topbar-ocean">
        <h1>
            <i class="fas fa-futbol"></i> SPORT KICKS API CONTRACT & SWAGGER UI
        </h1>
        <div>
            <a href="../index.php"><i class="fas fa-store me-1"></i> Trang Chủ Shop</a>
            <a href="openapi.json" target="_blank" class="ms-2"><i class="fas fa-code me-1"></i> File JSON OpenAPI</a>
        </div>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            window.ui = SwaggerUIBundle({
                url: "openapi.json",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "BaseLayout"
            });
        };
    </script>
</body>
</html>

