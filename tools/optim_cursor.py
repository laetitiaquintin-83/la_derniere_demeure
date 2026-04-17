from PIL import Image, ImageDraw

# Créer une image 32x32 avec un fond complètement transparent
img = Image.new('RGBA', (32, 32), (0, 0, 0, 0))
draw = ImageDraw.Draw(img)

# Coordonnées pour dessiner une petite rose/fleur
# Centre approximatif
center_x, center_y = 16, 16
radius = 6

# Dessiner la rose avec des pétales en rose/or
petal_color = (209, 184, 122, 255)  # Couleur or #d1b87a
core_color = (255, 255, 255, 200)    # Blanc transparent

# Pétales autour
for i in range(6):
    angle = i * 60
    import math
    x = center_x + radius * math.cos(math.radians(angle))
    y = center_y + radius * math.sin(math.radians(angle))
    draw.ellipse([x-3, y-3, x+3, y+3], fill=petal_color)

# Coeur central blanc
draw.ellipse([center_x-2, center_y-2, center_x+2, center_y+2], fill=core_color)

# Sauvegarder
img.save('images/rose-curseur.png')
print("✓ Curseur rose 32x32 créé avec succès!")

# Vérifier la taille
import os
size = os.path.getsize('images/rose-curseur.png')
print(f"Taille: {size} bytes")
