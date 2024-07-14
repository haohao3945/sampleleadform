import os
import json

def generate_gallery_data(gallery_path):
    gallery_data = []
    project_folders = sorted([f.path for f in os.scandir(gallery_path) if f.is_dir()])
    for index, project_folder in enumerate(project_folders):
        project_name = os.path.basename(project_folder)
        image_paths = []
        for filename in os.listdir(project_folder):
            if filename.lower().endswith('.webp'):
                image_path = os.path.join(project_folder, filename).replace("\\", "/")
                image_paths.append(image_path)
        gallery_data.append({
            'project_name': project_name,
            'images': image_paths
        })
    return gallery_data

def generate_gallery_html(gallery_data, images_per_page=6):
    html = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>

<section id="gallery" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Past Projects</h2>
        <p class="text-center" style="margin-bottom:30px;">Over the past 7 years, we have helped over 50 clients, including shop lot owners, homeowners, charity organizations, and commercial property managers, in maintaining and enhancing their properties.</p>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs justify-content-center" id="galleryTabs" role="tablist">
"""

    # Prepare tab content
    tab_content = """
        </ul> <!-- End of nav-tabs -->
        
        <!-- Tab Content -->
        <div class="tab-content mt-4" id="galleryTabContent">
"""

    for index, project_data in enumerate(gallery_data):
        project_name = project_data['project_name']
        tab_id = f"project_{index + 1}"
        
        # Add tab navigation item
        html += f"""
            <li class="nav-item">
                <a class="nav-link" style="color:black;" id="{tab_id}-tab" data-toggle="tab" href="#" role="tab" aria-controls="{tab_id}" aria-selected="false" onclick="loadImages({index}); return false;">{project_name}</a>
            </li>
"""

        # Generate JavaScript to load images for each project
        tab_content += f"""
            <div class="tab-pane fade" id="{tab_id}" role="tabpanel" aria-labelledby="{tab_id}-tab">
                <!-- Gallery Grid for {project_name} -->
                <div class="row" id="project_{index}_images">
                    <!-- Images will be dynamically loaded here -->
                </div>
            </div> <!-- End of tab-pane -->
"""

    tab_content += """
        </div> <!-- End of tab-content -->
    </div> <!-- End of container -->
</section> <!-- End of gallery section -->

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-61v5rOwDJkkv29Ojg3tNrP4xNmvwytF65+XPH0WwSluA5ggzxFX7RC1wDzU4Ey8/" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+oLr5FOkMyv/R/wI1N+gRXg06T5JqyQfG+J" crossorigin="anonymous"></script>

<script>
    // Activate Bootstrap tab functionality
    $(document).ready(function(){
        $('#galleryTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });

    // Function to load images dynamically
    function loadImages(index) {
        var projectImages = document.getElementById('project_' + (index + 1) + '_images');
        var imagesHTML = '';

        // Replace with actual JSON data
        var imageData = """ + json.dumps(gallery_data) + """;
        
        for (var i = 0; i < imageData[index]['images'].length; i++) {
            imagesHTML += '<div class="col-md-4 mb-4">';
            imagesHTML += '<img src="' + imageData[index]['images'][i] + '" alt="' + imageData[index]['project_name'] + '" class="img-fluid">';
            imagesHTML += '</div>';
        }

        projectImages.innerHTML = imagesHTML;
    }
</script>

</body>
</html>
"""

    html += tab_content

    return html

# Define the path to your gallery directory
gallery_path = 'gallery'  # Replace with your actual gallery directory path

# Generate JSON data for gallery
gallery_data = generate_gallery_data(gallery_path)

# Generate HTML content with dynamic image loading
gallery_html = generate_gallery_html(gallery_data)

# Write HTML content to an HTML file
output_file = 'gallery_html_output.html'
with open(output_file, 'w') as file:
    file.write(gallery_html)

print(f"Gallery HTML structure has been generated and saved to {output_file}")
