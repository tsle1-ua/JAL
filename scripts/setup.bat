@echo off
rem Simple wrapper to run the setup script using Git Bash or WSL
if exist "%ProgramFiles%\Git\bin\bash.exe" (
    "%ProgramFiles%\Git\bin\bash.exe" "%~dp0setup.sh" %*
) else if exist "C:\\Windows\\System32\\wsl.exe" (
    wsl bash "%~dp0setup.sh" %*
) else (
    echo Please run scripts/setup.sh via a bash environment.
    exit /b 1
)
