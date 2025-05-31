<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentationTitle }} - Talana</title>
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/>
    
    <!-- Google Fonts para branding Talana -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
    /* Variables CSS para branding Talana - Paleta actualizada de https://www.color-hex.com/color-palette/49840 */
    :root {
        --talana-primary: #babae8;
        --talana-secondary: #5f60cb;
        --talana-accent: #ffd3a5;
        --talana-success: #fd9853;
        --talana-light-pink: #a8e6cf;
        --talana-light: #F8F9FA;
        --talana-dark: #212529;
        --talana-gray: #6C757D;
        --talana-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    html {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
        font-family: var(--talana-font);
    }
    
    *,
    *:before,
    *:after {
        box-sizing: inherit;
    }

    body {
        margin: 0;
        background: var(--talana-light);
        font-family: var(--talana-font);
    }

    /* Header personalizado Talana */
    .talana-header {
        background: linear-gradient(135deg, var(--talana-primary) 0%, var(--talana-secondary) 100%);
        color: white;
        padding: 20px 0;
        box-shadow: 0 4px 12px rgba(255, 140, 148, 0.15);
        position: relative;
        overflow: hidden;
    }

    .talana-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .talana-header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 1;
    }

    .talana-logo {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .talana-logo-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 20px;
    }

    .talana-brand {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .talana-tagline {
        font-size: 14px;
        opacity: 0.9;
        margin: 2px 0 0 0;
        font-weight: 400;
    }

    .talana-version {
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Personalización de Swagger UI */
    .swagger-ui .topbar {
        display: none;
    }

    .swagger-ui {
        font-family: var(--talana-font);
    }

    .swagger-ui .info {
        margin: 50px 0;
    }

    .swagger-ui .info .title {
        color: var(--talana-primary);
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .swagger-ui .info .description {
        color: var(--talana-gray);
        font-size: 16px;
        line-height: 1.6;
    }

    /* Botones personalizados */
    .swagger-ui .btn.authorize {
        background: var(--talana-primary);
        border-color: var(--talana-primary);
        color: white;
        font-weight: 600;
        border-radius: 8px;
        padding: 12px 24px;
        transition: all 0.3s ease;
    }

    .swagger-ui .btn.authorize:hover {
        background: var(--talana-secondary);
        border-color: var(--talana-secondary);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 140, 148, 0.3);
    }

    /* Bloques de operaciones */
    .swagger-ui .opblock.opblock-get {
        border-color: var(--talana-light-pink);
        background: rgba(168, 230, 207, 0.05);
    }

    .swagger-ui .opblock.opblock-get .opblock-summary-method {
        background: var(--talana-light-pink);
        color: var(--talana-dark);
    }

    .swagger-ui .opblock.opblock-post {
        border-color: var(--talana-success);
        background: rgba(253, 152, 83, 0.05);
    }

    .swagger-ui .opblock.opblock-post .opblock-summary-method {
        background: var(--talana-success);
        color: white;
    }

    .swagger-ui .opblock.opblock-put {
        border-color: var(--talana-accent);
        background: rgba(255, 211, 165, 0.05);
    }

    .swagger-ui .opblock.opblock-put .opblock-summary-method {
        background: var(--talana-accent);
        color: var(--talana-dark);
    }

    .swagger-ui .opblock.opblock-delete {
        border-color: var(--talana-primary);
        background: rgba(255, 140, 148, 0.05);
    }

    .swagger-ui .opblock.opblock-delete .opblock-summary-method {
        background: var(--talana-primary);
        color: white;
    }

    /* Tags personalizados */
    .swagger-ui .opblock-tag {
        color: var(--talana-primary);
        font-weight: 600;
        font-size: 18px;
        border-bottom: 2px solid var(--talana-primary);
        padding-bottom: 10px;
        margin: 30px 0 20px 0;
    }

    /* Esquemas */
    .swagger-ui .model-box {
        background: var(--talana-light);
        border: 1px solid rgba(255, 140, 148, 0.1);
        border-radius: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .talana-header-content {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        
        .talana-brand {
            font-size: 24px;
        }
    }

    /* Animaciones */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .swagger-ui {
        animation: fadeInUp 0.6s ease-out;
    }
    </style>
</head>

<body @if(config('l5-swagger.defaults.ui.display.dark_mode')) id="dark-mode" @endif>

    <!-- Header personalizado Talana -->
    <header class="talana-header">
        <div class="talana-header-content">
            <div class="talana-logo">
                <div class="talana-logo-icon">
                    <img src="{{ asset('storage/images/profile-icon.png') }}" alt="Talana Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <h1 class="talana-brand">Talana</h1>
                    <p class="talana-tagline">TalaTrivia API Documentation</p>
                </div>
            </div>
            <div class="talana-version">
                v1.0.0
            </div>
        </div>
    </header>

    <div id="swagger-ui"></div>

    <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
    <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
    
    <script>
        // Swagger UI Configuration
        window.onload = function() {
            const urls = [];

            @foreach($urlsToDocs as $title => $url)
                urls.push({name: "{{ $title }}", url: "{{ $url }}"});
            @endforeach

            // Build a system
            const ui = SwaggerUIBundle({
                dom_id: '#swagger-ui',
                urls: urls,
                "urls.primaryName": "{{ $documentationTitle }}",
                operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
                configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
                validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
                oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

                requestInterceptor: function(request) {
                    request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                    return request;
                },

                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],

                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],

                layout: "StandaloneLayout",
                docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
                deepLinking: true,
                filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
                persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

            });

            window.ui = ui;

            @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
            ui.initOAuth({
                usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
            });
            @endif
        }
    </script>
</body>
</html>