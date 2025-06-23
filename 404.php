<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - InvexQR</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            margin: 2rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #f39c12;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 1rem 0;
        }
        .error-message {
            color: #7f8c8d;
            margin: 1.5rem 0;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 0.5rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Page non trouvée</h2>
        <p class="error-message">
            La page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        <div>
            <a href="/" class="btn">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>