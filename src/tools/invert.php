<!-- Dependencies -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    
    <!-- Top Bar: Title & Global Actions -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Image Studio</h1>
            <p class="text-neutral-400 text-sm mt-1">Advanced browser-based editor.</p>
        </div>
        
        <!-- Undo/Redo & History Controls -->
        <div class="flex items-center gap-2 bg-neutral-900 p-1.5 rounded-lg border border-neutral-800">
            <button onclick="undo()" id="btnUndo" class="p-2 text-neutral-500 hover:text-white disabled:opacity-30 disabled:hover:text-neutral-500 transition-colors" title="Undo (Ctrl+Z)" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            </button>
            <div class="w-px h-6 bg-neutral-800"></div>
            <button onclick="redo()" id="btnRedo" class="p-2 text-neutral-500 hover:text-white disabled:opacity-30 disabled:hover:text-neutral-500 transition-colors" title="Redo (Ctrl+Y)" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path></svg>
            </button>
        </div>
    </div>

    <!-- Main Editor Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Sidebar: Tools -->
        <div class="lg:col-span-1 space-y-4">
            
            <!-- Upload -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-4">
                <input type="file" id="imageInput" accept="image/*" class="hidden">
                <button onclick="document.getElementById('imageInput').click()" class="w-full py-3 border border-dashed border-neutral-700 text-neutral-400 rounded-lg hover:border-white hover:text-white transition-colors flex flex-col items-center gap-2 group">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span class="text-sm font-medium">New Image</span>
                </button>
            </div>

            <!-- Editor Controls -->
            <div id="controls" class="opacity-50 pointer-events-none transition-opacity duration-300 space-y-4">
                
                <!-- Filters -->
                <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-4">
                    <h3 class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider mb-3">Filters</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="toggleFilter('invert')" id="btnInvert" class="px-3 py-2 bg-neutral-900 border border-neutral-800 rounded text-sm text-neutral-300 hover:text-white transition-all">
                            Invert
                        </button>
                        <button onclick="toggleFilter('gray')" id="btnGray" class="px-3 py-2 bg-neutral-900 border border-neutral-800 rounded text-sm text-neutral-300 hover:text-white transition-all">
                            Grayscale
                        </button>
                    </div>
                </div>

                <!-- Transform -->
                <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-4">
                    <h3 class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider mb-3">Transform</h3>
                    <div class="space-y-2">
                        <!-- Crop -->
                        <button onclick="toggleCrop()" id="btnCrop" class="w-full px-3 py-2 bg-neutral-900 border border-neutral-800 rounded text-sm text-neutral-300 hover:text-white transition-all flex justify-between items-center group">
                            <span>Crop Tool</span>
                            <span id="cropStatus" class="text-[10px] px-2 py-0.5 rounded bg-neutral-800 text-neutral-400">OFF</span>
                        </button>
                        
                        <!-- Rotate -->
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="applyRotation(-90)" class="px-3 py-2 bg-neutral-900 border border-neutral-800 rounded text-neutral-300 hover:bg-neutral-800 text-sm">
                                ↺ -90°
                            </button>
                            <button onclick="applyRotation(90)" class="px-3 py-2 bg-neutral-900 border border-neutral-800 rounded text-neutral-300 hover:bg-neutral-800 text-sm">
                                ↻ +90°
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Download Options -->
                <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-4">
                    <h3 class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider mb-3">Export</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] text-neutral-500 block mb-1">Filename</label>
                            <input type="text" id="filename" value="edited-image" class="w-full bg-black border border-neutral-800 text-white text-sm px-3 py-2 rounded focus:border-neutral-500 focus:outline-none placeholder-neutral-700">
                        </div>
                        <button onclick="downloadImage()" class="w-full bg-white text-black font-bold py-3 rounded-lg shadow hover:bg-neutral-200 transition-transform active:scale-95 text-sm flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Download PNG
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Main Canvas Area -->
        <div class="lg:col-span-3">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-1 h-[650px] flex items-center justify-center relative overflow-hidden">
                
                <!-- Placeholder -->
                <div id="placeholder" class="text-center text-neutral-600">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p>No image loaded</p>
                </div>

                <!-- Image Wrapper -->
                <div id="filterWrapper" class="relative max-w-full max-h-full transition-all duration-200">
                    <img id="editorImage" class="max-w-full max-h-[630px] hidden object-contain block">
                </div>
                
                <!-- Loader -->
                <div id="loader" class="absolute inset-0 bg-black/80 hidden items-center justify-center z-50">
                    <div class="text-center">
                        <div class="w-8 h-8 border-2 border-white border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                        <span class="text-xs text-neutral-400">Processing...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- State Management ---
    const State = {
        history: [],
        currentIndex: -1,
        cropper: null,
        filters: { invert: false, gray: false },
        originalName: 'image'
    };

    // Elements
    const els = {
        img: document.getElementById('editorImage'),
        wrapper: document.getElementById('filterWrapper'),
        controls: document.getElementById('controls'),
        placeholder: document.getElementById('placeholder'),
        input: document.getElementById('imageInput'),
        filename: document.getElementById('filename'),
        btnUndo: document.getElementById('btnUndo'),
        btnRedo: document.getElementById('btnRedo'),
        cropStatus: document.getElementById('cropStatus'),
        btnCrop: document.getElementById('btnCrop')
    };

    // --- History System ---
    function pushState(imgSrc) {
        // If we are in the middle of history and do a new action, slice off the future
        if (State.currentIndex < State.history.length - 1) {
            State.history = State.history.slice(0, State.currentIndex + 1);
        }
        
        State.history.push(imgSrc);
        State.currentIndex++;
        updateUndoRedoUI();
    }

    function undo() {
        if (State.currentIndex > 0) {
            // Destroy cropper if active during undo
            if(State.cropper) toggleCrop(); 
            
            State.currentIndex--;
            loadImage(State.history[State.currentIndex], false);
            updateUndoRedoUI();
        }
    }

    function redo() {
        if (State.currentIndex < State.history.length - 1) {
            if(State.cropper) toggleCrop();

            State.currentIndex++;
            loadImage(State.history[State.currentIndex], false);
            updateUndoRedoUI();
        }
    }

    function updateUndoRedoUI() {
        els.btnUndo.disabled = State.currentIndex <= 0;
        els.btnRedo.disabled = State.currentIndex >= State.history.length - 1;
    }

    // --- Loading ---
    els.input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            State.originalName = file.name.split('.')[0];
            els.filename.value = State.originalName + "-edited";
            
            const reader = new FileReader();
            reader.onload = function(evt) {
                // Reset everything
                State.history = [];
                State.currentIndex = -1;
                pushState(evt.target.result); // Initial State
                loadImage(evt.target.result);
                
                els.controls.classList.remove('opacity-50', 'pointer-events-none');
                els.placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    function loadImage(src, push = true) {
        els.img.src = src;
        els.img.classList.remove('hidden');
    }

    // --- Filters ---
    function toggleFilter(type) {
        if(type === 'invert') State.filters.invert = !State.filters.invert;
        if(type === 'gray') State.filters.gray = !State.filters.gray;
        
        applyFiltersToView();
        updateFilterButtons();
    }

    function applyFiltersToView() {
        let css = '';
        if(State.filters.invert) css += 'invert(100%) ';
        if(State.filters.gray) css += 'grayscale(100%) ';
        els.wrapper.style.filter = css;
    }

    function updateFilterButtons() {
        const activeClass = "px-3 py-2 bg-white text-black border border-white rounded text-sm font-semibold transition-all";
        const inactiveClass = "px-3 py-2 bg-neutral-900 border border-neutral-800 rounded text-sm text-neutral-300 hover:text-white transition-all";
        
        document.getElementById('btnInvert').className = State.filters.invert ? activeClass : inactiveClass;
        document.getElementById('btnGray').className = State.filters.gray ? activeClass : inactiveClass;
    }

    // --- Crop System ---
    function toggleCrop() {
        if (State.cropper) {
            // APPLY CROP
            const canvas = State.cropper.getCroppedCanvas();
            const newSrc = canvas.toDataURL();
            
            State.cropper.destroy();
            State.cropper = null;
            
            loadImage(newSrc);
            pushState(newSrc); // Save to history
            
            els.cropStatus.innerText = "OFF";
            els.cropStatus.className = "text-[10px] px-2 py-0.5 rounded bg-neutral-800 text-neutral-400";
            els.btnCrop.classList.remove('border-green-900', 'bg-green-900/10');
        } else {
            // START CROP
            State.cropper = new Cropper(els.img, {
                viewMode: 1,
                autoCropArea: 0.8,
                background: false
            });
            
            els.cropStatus.innerText = "APPLY";
            els.cropStatus.className = "text-[10px] px-2 py-0.5 rounded bg-green-500 text-black font-bold";
            els.btnCrop.classList.add('border-green-900', 'bg-green-900/10');
        }
    }

    // --- Silent Rotation (No Crop UI) ---
    function applyRotation(deg) {
        // If cropper IS active, use it (users expect it)
        if(State.cropper) {
            State.cropper.rotate(deg);
            return;
        }

        // If cropper is NOT active, use internal canvas to rotate silently
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const image = els.img;

        // Determine new dimensions
        if (Math.abs(deg) === 90) {
            canvas.width = image.naturalHeight;
            canvas.height = image.naturalWidth;
        } else {
            canvas.width = image.naturalWidth;
            canvas.height = image.naturalHeight;
        }

        // Rotate context
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(deg * Math.PI / 180);
        ctx.drawImage(image, -image.naturalWidth / 2, -image.naturalHeight / 2);

        // Update State
        const newSrc = canvas.toDataURL();
        loadImage(newSrc);
        pushState(newSrc);
    }

    // --- Download ---
    function downloadImage() {
        // 1. Get Base Canvas
        let canvas;
        if (State.cropper) {
            canvas = State.cropper.getCroppedCanvas();
        } else {
            canvas = document.createElement('canvas');
            canvas.width = els.img.naturalWidth;
            canvas.height = els.img.naturalHeight;
            canvas.getContext('2d').drawImage(els.img, 0, 0);
        }

        // 2. Apply Filters (Burn in)
        if (State.filters.invert || State.filters.gray) {
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;
            const ctx = tempCanvas.getContext('2d');
            
            let filterStr = '';
            if (State.filters.invert) filterStr += 'invert(100%) ';
            if (State.filters.gray) filterStr += 'grayscale(100%) ';
            ctx.filter = filterStr.trim();
            
            ctx.drawImage(canvas, 0, 0);
            canvas = tempCanvas;
        }

        // 3. Download
        const link = document.createElement('a');
        let name = els.filename.value || 'edited-image';
        if (!name.endsWith('.png')) name += '.png';
        
        link.download = name;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
    
    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
            e.preventDefault();
            undo();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
            e.preventDefault();
            redo();
        }
    });
</script>