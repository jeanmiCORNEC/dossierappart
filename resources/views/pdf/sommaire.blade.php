<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            /* CORRECTION ICI : On utilise DejaVu Sans pour le support UTF-8 */
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        h1 {
            color: #1F2937;
            font-size: 28px;
            margin: 0;
        }

        .meta {
            color: #6B7280;
            font-size: 14px;
            margin-top: 10px;
        }

        .document-list {
            margin-top: 40px;
            background: #F9FAFB;
            padding: 30px;
            border-radius: 8px;
        }

        .doc-type {
            font-weight: bold;
            font-size: 18px;
            color: #4F46E5;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 5px;
        }

        .doc-type:first-child {
            margin-top: 0;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            padding: 8px 0;
            padding-left: 20px;
            position: relative;
        }

        /* Le point (bullet) doit aussi être géré par la police */
        li:before {
            content: "•";
            color: #4F46E5;
            position: absolute;
            left: 0;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #9CA3AF;
            padding: 20px;
            border-top: 1px solid #E5E7EB;
        }

        .watermark-info {
            margin-top: 50px;
            text-align: center;
            font-style: italic;
            color: #6B7280;
            font-size: 12px;
            border: 1px dashed #D1D5DB;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">dossierappart</div>
        <h1>Dossier de Location - {{ $dossier->pays->nom }}</h1>
        <div class="meta">
            Généré le {{ $generatedAt->format('d/m/Y à H:i') }}
        </div>
    </div>

    <div class="document-list">
        <h2 style="margin-top: 0; margin-bottom: 20px;">Sommaire des pièces fournies</h2>

        @foreach($documentsByType as $typeId => $docs)
        <div class="doc-type">
            {{ $docs->first()->typeDocumentPays->libelle }}
            <span style="font-size: 14px; font-weight: normal; color: #6B7280; float: right;">
                {{ $docs->count() }} document{{ $docs->count() > 1 ? 's' : '' }}
            </span>
        </div>
        <ul>
            @foreach($docs as $doc)
            <li>{{ $doc->original_filename }}</li>
            @endforeach
        </ul>
        @endforeach
    </div>

    <div class="watermark-info">
        Ce dossier est protégé par un filigrane numérique fusionné avec les documents.<br>
        Toute tentative de modification sera visible.
    </div>

    <div class="footer">
        Dossier réalisé sur <a href="https://dossierappart.fr" style="color: #4F46E5; text-decoration: none;">dossierappart.fr</a>
    </div>
</body>

</html>