import os
from PIL import Image

def convert_to_webp(input_path, output_path):
    try:
        img = Image.open(input_path)
        img.save(output_path, 'WEBP')
        return True
    except Exception as e:
        print(f"Failed to convert {input_path} to WebP: {str(e)}")
        return False

def convert_images_to_webp(directory):
    # Iterate through all files in the directory
    for root, _, files in os.walk(directory):
        for file in files:
            if file.lower().endswith(('.png', '.jpg', '.jpeg', '.gif', '.bmp')):
                input_path = os.path.join(root, file)
                output_path = os.path.splitext(input_path)[0] + ".webp"

                # Convert to WebP (overwrite existing file)
                if convert_to_webp(input_path, output_path):
                    print(f"{file} converted to WebP successfully.")
                    # Optionally, delete the original file after conversion
                    os.remove(input_path)
                else:
                    print(f"{file} conversion failed.")

# Define the path to your gallery directory
gallery_path = 'gallery'  # Replace with your actual gallery directory path

# Convert images to WebP (overwrite existing files)
convert_images_to_webp(gallery_path)
