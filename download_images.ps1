$dest = "C:/Users/danica/Desktop/aag-dev/assets/img"

$images = @(
  @{ name = "ground-handling.jpg";        url = "https://images.unsplash.com/photo-1569629743817-70d8db6c323b?w=1920&q=85&fit=crop" },
  @{ name = "hotel.jpg";                  url = "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&q=85&fit=crop" },
  @{ name = "vip-transport.jpg";          url = "https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920&q=85&fit=crop" },
  @{ name = "charter.jpg";               url = "https://images.unsplash.com/photo-1540962351504-03099e0a754b?w=1920&q=85&fit=crop" },
  @{ name = "fuel.jpg";                  url = "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1920&q=85&fit=crop" },
  @{ name = "inquiry.jpg";               url = "https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=85&fit=crop" },
  @{ name = "services.jpg";              url = "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1920&q=85&fit=crop" },
  @{ name = "ph-airports.jpg";           url = "https://images.unsplash.com/photo-1474302770737-173ee21bab63?w=1920&q=85&fit=crop" },
  @{ name = "permits.jpg";               url = "https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1920&q=85&fit=crop" },
  @{ name = "concierge-vip-security.jpg"; url = "https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1920&q=85&fit=crop" },
  @{ name = "inflight-catering.jpg";     url = "https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1920&q=85&fit=crop" }
)

foreach ($img in $images) {
  Write-Host "Downloading $($img.name)..."
  Invoke-WebRequest -Uri $img.url -OutFile "$dest/$($img.name)" -UseBasicParsing
  Write-Host "Done: $($img.name)"
}

Write-Host "All images downloaded!"
