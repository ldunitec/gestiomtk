@echo off
setlocal
cd /d "%~dp0"
if not exist "artisan" exit /b 2
set "AGENDA_PHP=H:\xampp\php\php.exe"
if not exist "%AGENDA_PHP%" (
    echo No se encontro PHP en %AGENDA_PHP%. Corrige AGENDA_PHP en este archivo.
    exit /b 2
)
"%AGENDA_PHP%" -d opcache.enable_cli=0 artisan agenda:completar-vencidas >> "storage\logs\agenda-cierre-diario.log" 2>&1
set "AGENDA_RESULTADO=%ERRORLEVEL%"
exit /b %AGENDA_RESULTADO%
