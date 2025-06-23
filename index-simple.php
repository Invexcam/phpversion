<?php
// Solution de secours pour l'erreur 404
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvexQR - Générateur de QR Codes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: rgba(255,255,255,0.95);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 10px;
        }
        .tagline {
            font-size: 1.2rem;
            color: #666;
        }
        .content {
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
        }
        .status {
            background: #fef3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: #6366f1;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #4f46e5;
        }
        .btn-secondary {
            background: #64748b;
        }
        .btn-secondary:hover {
            background: #475569;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .feature {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .feature h3 {
            color: #6366f1;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">InvexQR</div>
            <div class="tagline">Générateur professionnel de QR codes dynamiques</div>
        </div>

        <div class="content">
            <div class="status">
                <h3>🔧 Site en maintenance</h3>
                <p>Nous mettons à jour notre plateforme pour vous offrir une meilleure expérience.</p>
                <p>Le service sera rétabli sous peu.</p>
            </div>

            <h2>Fonctionnalités disponibles bientôt</h2>
            
            <div class="features">
                <div class="feature">
                    <h3>Plan Gratuit</h3>
                    <p>Jusqu'à 3 QR codes</p>
                    <p>Analytics de base</p>
                    <p>QR codes dynamiques</p>
                </div>
                
                <div class="feature">
                    <h3>Plan Premium - 5€/mois</h3>
                    <p>QR codes illimités</p>
                    <p>Logos personnalisés</p>
                    <p>Analytics avancées</p>
                </div>
                
                <div class="feature">
                    <h3>Fonctionnalités Pro</h3>
                    <p>Export haute résolution</p>
                    <p>API d'intégration</p>
                    <p>Support prioritaire</p>
                </div>
            </div>

            <div>
                <a href="mailto:contact@invexqr.com" class="btn">Nous contacter</a>
                <a href="/diagnostic.php" class="btn btn-secondary">Diagnostic technique</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 InvexQR - Tous droits réservés</p>
            <p>Support technique: contact@invexqr.com</p>
        </div>
    </div>

    <script>
        // Vérification automatique du statut
        setTimeout(() => {
            fetch('/api/public/stats')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.querySelector('.status').innerHTML = 
                            '<h3>✅ Service opérationnel</h3><p>La plateforme fonctionne correctement.</p><p><a href="/" class="btn">Accéder à l\'application</a></p>';
                    }
                })
                .catch(() => {
                    // Service pas encore prêt
                });
        }, 2000);
    </script>
</body>
</html>