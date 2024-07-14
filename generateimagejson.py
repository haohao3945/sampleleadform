import os
import json

def generate_gallery_json(gallery_path):
    gallery_data = []

    # Iterate through all project folders
    project_folders = sorted([f.path for f in os.scandir(gallery_path) if f.is_dir()])
    for index, project_folder in enumerate(project_folders):
        project_name = os.path.basename(project_folder)
        images = []

        # Iterate through image files in each project folder
        for filename in os.listdir(project_folder):
            if filename.lower().endswith(('.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp')):
                image_path = os.path.join(project_folder, filename)
                images.append({
                    'filename': filename,
                    'path': image_path
                })

        # Append project data to gallery_data
        gallery_data.append({
            'project_name': project_name,
            'images': images
        })

    # Write gallery data to JSON file
    output_file = 'gallery_data.json'
    with open(output_file, 'w') as json_file:
        json.dump(gallery_data, json_file, indent=4)

    print(f"Gallery data has been generated and saved to {output_file}")

# Define the path to your gallery directory
gallery_path = 'gallery'  # Replace with your actual gallery directory path

# Generate JSON file for gallery data
generate_gallery_json(gallery_path)
