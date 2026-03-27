Invoke-WebRequest -Uri "https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=1920&q=85" -OutFile "C:/Users/danica/Desktop/aag-dev/assets/img/services.jpg" -UseBasicParsing
Write-Host "Downloaded: services.jpg"

Invoke-WebRequest -Uri "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=1920&q=85" -OutFile "C:/Users/danica/Desktop/aag-dev/assets/img/concierge-vip-security.jpg" -UseBasicParsing
Write-Host "Downloaded: concierge-vip-security.jpg"

Invoke-WebRequest -Uri "https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800&q=85" -OutFile "C:/Users/danica/Desktop/aag-dev/assets/img/services/services-concierge.jpg" -UseBasicParsing
Write-Host "Downloaded: services-concierge.jpg"

Invoke-WebRequest -Uri "https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=85" -OutFile "C:/Users/danica/Desktop/aag-dev/assets/img/services/services-catering.jpg" -UseBasicParsing
Write-Host "Downloaded: services-catering.jpg"

Write-Host "All images downloaded!"
