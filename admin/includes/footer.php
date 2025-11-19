</div>
        </div>
    </div>
    
    <!-- Footer Credit -->
    <footer class="bg-black text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">
                <?php echo getSiteSetting('site_name', SITE_NAME); ?> - Developed by Saieed Rahman | © 2025 SidMan Solutions
            </p>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo ASSETS_URL; ?>/js/admin.js"></script>
    
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '.tinymce-editor',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            menubar: false,
            height: 400,
            language: 'en',
            content_style: 'body { font-family: "Hind Siliguri", "Roboto", Arial, sans-serif; font-size: 14px; }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
        
        // Form validation
        function validateForm(form) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                showCustomAlert('সব প্রয়োজনীয় ফিল্ড পূরণ করুন।');
            }
            
            return isValid;
        }
        
        // Image preview
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
        
        // Custom confirm dialog
        function showConfirmDialog(message, onConfirm) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-sm w-full mx-4">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">${message}</p>
                    <div class="flex justify-end space-x-3">
                        <button class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" onclick="this.closest('.fixed').remove()">বাতিল</button>
                        <button class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded" onclick="this.closest('.fixed').remove(); (${onConfirm})()">নিশ্চিত</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        // Auto-generate slug
        function generateSlug(title, slugField) {
            const slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            
            document.getElementById(slugField).value = slug;
        }
        
        // Show success message
        function showSuccessMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            alertDiv.textContent = message;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
        
        // Show error message
        function showErrorMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            alertDiv.textContent = message;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        // Custom alert function
        function showCustomAlert(message) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg max-w-sm w-full mx-4 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">বিজ্ঞপ্তি</h3>
                    <p class="text-gray-700 mb-4">${message}</p>
                    <div class="flex justify-end">
                        <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">ঠিক আছে</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
    </script>
</body>
</html>