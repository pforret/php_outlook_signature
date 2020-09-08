:: copy all required files for the Outlook Signature
:: source      = {source} -- typically "%SIGNATURE_EXPORTS%\%TEMPLATE%\%USERNAME%"
:: destination = {destin} -- typically "%APPDATA%\Microsoft\Signatures"
:: copy images and text files
:: script will open in the right subfolder upon double-click.
xcopy /Y /S *.png *.jpg *.gif *.htm *.txt "{destin}\"
