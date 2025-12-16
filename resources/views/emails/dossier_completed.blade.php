<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; padding: 20px; }
        .container { background-color: white; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .btn { 
    display: inline-block; 
    background-color: #4F46E5; 
    color: #ffffff !important; 
    padding: 14px 28px; 
    text-decoration: none; 
    border-radius: 6px; 
    font-weight: bold; 
    margin-top: 20px;
    font-size: 16px;
    border: 1px solid #4338ca;
}
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center; }
        .warning { background-color: #FEF2F2; color: #991B1B; padding: 10px; border-radius: 4px; margin-top: 20px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Votre dossier est sécurisé ! ✅</h2>
        
        <p>Bonjour,</p>
        <p>Le traitement de vos documents est terminé. Votre dossier de location unique, fusionné et protégé par filigrane est prêt.</p>
        
        <div style="text-align: center;">
            <a href="{{ $url }}" class="btn">Télécharger mon dossier PDF</a>
        </div>

        <div class="warning">
            ⚠️ <strong>Attention :</strong> Ce lien est valide jusqu'au <strong>{{ $expiration }}</strong>.
            Passé ce délai, vos données seront définitivement effacées de nos serveurs par mesure de sécurité.
        </div>

        <p style="margin-top: 20px; font-size: 14px;">
            Merci de votre confiance,<br>
            L'équipe DossierAppart.
        </p>
    </div>
    
    <div class="footer">
        Ceci est un envoi automatique. Merci de ne pas répondre.<br>
        DossierAppart - Sécurisation de documents locatifs.
    </div>
</body>
</html>