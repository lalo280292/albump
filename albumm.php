<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Imágenes y Videos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            justify-content: center;
            flex-grow: 1;
            padding: 10px;
        }
        .item {
            position: relative;
            overflow: hidden;
            height: 100px; /* Cambiar altura a la mitad */
            background: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .item img, .item video {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-height: 100%;
            width: auto;
        }
        .item video {
            background-color: #000;
        }
        .upload-form {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .upload-form input[type="file"] {
            display: none;
        }
        .upload-form label {
            display: inline-block;
            width: 60px;
            height: 60px;
            background-color: #007bff;
            border-radius: 50%;
            text-align: center;
            line-height: 60px;
            color: white;
            font-size: 24px;
            cursor: pointer;
            position: relative;
            margin-bottom: 10px;
        }
        .upload-form label img {
            width: 30px;
            height: 30px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .preview-window {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 500px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: none;
            flex-direction: column;
            align-items: center;
            z-index: 2000;
        }
        .preview-window img {
            max-width: 100px;
            margin: 10px;
        }
        .preview-window video {
            max-width: 100px;
            margin: 10px;
        }
        .preview-window input[type="submit"] {
            display: block;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .preview-window input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .progress-container {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
        }
        .progress-bar {
            width: 0;
            height: 10px;
            background-color: #007bff;
            border-radius: 5px;
        }
        /* Lightbox Styles */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 3000;
        }
        .lightbox img, .lightbox video {
            max-width: 90%;
            max-height: 90%;
        }
        .lightbox .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Galería de Imágenes y Videos</h1>
    
    <div class="gallery">
        <!-- Gallery items will be included here -->
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <span class="close" onclick="closeLightbox()">&times;</span>
        <img id="lightboxImg" src="" alt="">
        <video id="lightboxVideo" controls>
            <source id="lightboxVideoSource" src="" type="">
        </video>
    </div>

    <div class="upload-form">
        <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
            <input type="file" name="filesToUpload[]" id="filesToUpload" accept="image/*,video/*" multiple capture="camera">
            <label for="filesToUpload">
                <img src="camera-icon.png" alt="Subir">
            </label>
        </form>
    </div>
    
    <div class="preview-window" id="previewWindow">
        <div id="previewContainer"></div>
        <input type="submit" value="Subir Archivos MUY PELIGROSOS" form="uploadForm" id="uploadButton">
        <div class="progress-container" id="progressContainer">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>

    <?php include 'albumm.php'; ?>
    
    <script>
        function openLightbox(src) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightboxImg');
            const lightboxVideo = document.getElementById('lightboxVideo');
            const lightboxVideoSource = document.getElementById('lightboxVideoSource');

            if (src.endsWith('.mp4') || src.endsWith('.webm') || src.endsWith('.ogg') || src.endsWith('.mov') || src.endsWith('.hevc')) {
                lightboxImg.style.display = 'none';
                lightboxVideo.style.display = 'block';
                lightboxVideoSource.src = src;
                lightboxVideo.type = 'video/' + src.split('.').pop();
            } else {
                lightboxImg.style.display = 'block';
                lightboxVideo.style.display = 'none';
                lightboxImg.src = src;
            }

            lightbox.style.display = 'flex';
        }

        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
        }

        document.getElementById('filesToUpload').addEventListener('change', function(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('previewContainer');
            previewContainer.innerHTML = '';
            
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const fileType = file.type.split('/')[0];
                    let element;
                    if (fileType === 'image') {
                        element = document.createElement('img');
                        element.src = e.target.result;
                    } else if (fileType === 'video') {
                        element = document.createElement('video');
                        element.src = e.target.result;
                        element.controls = true;
                    }
                    previewContainer.appendChild(element);
                };
                reader.readAsDataURL(file);
            }

            document.getElementById('previewWindow').style.display = 'flex';
        });

        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                const percent = e.lengthComputable ? (e.loaded / e.total) * 100 : 0;
                document.getElementById('progressContainer').style.display = 'block';
                document.getElementById('progressBar').style.width = percent + '%';
            });

            xhr.open('POST', 'upload.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload(); // Recargar la página después de subir
                } else {
                    alert('Error al subir los archivos');
                }
            };
            xhr.send(formData);
        });
    </script>
</body>
</html>
