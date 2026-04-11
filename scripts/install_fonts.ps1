# PowerShell script to install Khmer OS fonts system-wide
# Usage: Run this script as Administrator

$fonts = @(
    @{ Name = "Khmer OS Moul Light (TrueType)"; File = "KhmerOSMoulLight.ttf"; Source = "..\resources\fonts\KhmerOSMoulLight.ttf" },
    @{ Name = "Khmer OS Battambang (TrueType)"; File = "KhmerOSbattambang.ttf"; Source = "..\resources\fonts\KhmerOSbattambang.ttf" }
)

$targetFolder = "C:\Windows\Fonts"
$registryPath = "HKLM:\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Fonts"

foreach ($font in $fonts) {
    $sourcePath = Join-Path $PSScriptRoot $font.Source
    $targetPath = Join-Path $targetFolder $font.File
    
    if (Test-Path $sourcePath) {
        Write-Host "Installing $($font.Name)..."
        try {
            Copy-Item -Path $sourcePath -Destination $targetPath -Force -ErrorAction Stop
            New-ItemProperty -Path $registryPath -Name $font.Name -Value $font.File -PropertyType String -Force -ErrorAction Stop
            Write-Host "Successfully installed $($font.Name)."
        } catch {
            Write-Warning "Failed to install $($font.Name). This script likely needs to be run as Administrator."
            Write-Warning $_.Exception.Message
        }
    } else {
        Write-Error "Source file not found: $sourcePath"
    }
}

Write-Host "Font installation process completed."
