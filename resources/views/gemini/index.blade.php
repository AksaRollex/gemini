<!-- resources/views/gemini/index.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Gemini AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Laravel Gemini AI Demo</h1>
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-tab-pane" type="button" role="tab" aria-controls="text-tab-pane" aria-selected="true">Generate Text</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#image-tab-pane" type="button" role="tab" aria-controls="image-tab-pane" aria-selected="false">Analyze Image</button>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
            <!-- Text Generation Tab -->
            <div class="tab-pane fade show active" id="text-tab-pane" role="tabpanel" aria-labelledby="text-tab" tabindex="0">
                <div class="card mt-3">
                    <div class="card-body">
                        <form id="textForm">
                            <div class="mb-3">
                                <label for="textPrompt" class="form-label">Enter your prompt:</label>
                                <textarea class="form-control" id="textPrompt" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" id="generateBtn">Generate</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Image Analysis Tab -->
            <div class="tab-pane fade" id="image-tab-pane" role="tabpanel" aria-labelledby="image-tab" tabindex="0">
                <div class="card mt-3">
                    <div class="card-body">
                        <form id="imageForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="imagePrompt" class="form-label">Enter your prompt about the image:</label>
                                <textarea class="form-control" id="imagePrompt" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="imageFile" class="form-label">Upload an image:</label>
                                <input class="form-control" type="file" id="imageFile" accept="image/*" required>
                                <div class="mt-2">
                                    <img id="imagePreview" src="" class="img-fluid d-none" alt="Preview">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="analyzeBtn">Analyze Image</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div class="card mt-4 d-none" id="resultCard">
            <div class="card-header">
                <h5>Result</h5>
            </div>
            <div class="card-body">
                <div id="resultContent"></div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Image preview
            $('#imageFile').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Text generation form
            $('#textForm').submit(function(e) {
                e.preventDefault();
                const prompt = $('#textPrompt').val();
                
                $('#generateBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');
                
                $.ajax({
                    url: "{{ route('gemini.generate-text') }}",
                    type: "POST",
                    data: {
                        prompt: prompt
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#resultContent').html('<pre class="mb-0">' + response.result + '</pre>');
                            $('#resultCard').removeClass('d-none');
                        } else {
                            $('#resultContent').html('<div class="alert alert-danger">Failed to generate text.</div>');
                            $('#resultCard').removeClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        $('#resultContent').html('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
                        $('#resultCard').removeClass('d-none');
                    },
                    complete: function() {
                        $('#generateBtn').prop('disabled', false).text('Generate');
                    }
                });
            });
            
            // Image analysis form
            $('#imageForm').submit(function(e) {
                e.preventDefault();
                
                const prompt = $('#imagePrompt').val();
                const imageFile = $('#imageFile')[0].files[0];
                
                if (!imageFile) {
                    alert('Please select an image to upload');
                    return;
                }
                
                const formData = new FormData();
                formData.append('prompt', prompt);
                formData.append('image', imageFile);
                
                $('#analyzeBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analyzing...');
                
                $.ajax({
                    url: "{{ route('gemini.analyze-image') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            $('#resultContent').html('<pre class="mb-0">' + response.result + '</pre>');
                            $('#resultCard').removeClass('d-none');
                        } else {
                            $('#resultContent').html('<div class="alert alert-danger">Failed to analyze image.</div>');
                            $('#resultCard').removeClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        $('#resultContent').html('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
                        $('#resultCard').removeClass('d-none');
                    },
                    complete: function() {
                        $('#analyzeBtn').prop('disabled', false).text('Analyze Image');
                    }
                });
            });
        });
    </script>
</body>
</html>