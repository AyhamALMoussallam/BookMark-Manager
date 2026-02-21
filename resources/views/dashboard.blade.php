<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f0f2f5; }
header { background:#4CAF50; color:white; padding:15px; text-align:center; }
.container { padding:20px; }
h2 { margin-top:30px; }
table { width:100%; border-collapse:collapse; margin-top:10px; background:white; }
th, td { border:1px solid #ccc; padding:8px; text-align:left; }
th { background:#eee; }
button { padding:5px 8px; margin:2px; border:none; border-radius:4px; cursor:pointer; }
button.edit { background:#2196F3; color:white; }
button.delete { background:#f44336; color:white; }
button.favorite { background:#ff9800; color:white; }
button.add-btn { background:#4CAF50; color:white; margin-bottom:5px; }
/* Modal styles */
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; }
.modal-content { background:white; padding:20px; border-radius:10px; width:300px; }
.modal-content input, .modal-content textarea { width:100%; margin:5px 0; padding:5px; }
.close { float:right; cursor:pointer; font-weight:bold; color:#f44336; }
.message { color:red; text-align:center; margin-top:5px; }

/* Search & filters */
.search-filters { background:white; padding:16px; border-radius:8px; margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,0.08); }
.search-filters .row { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.search-filters label { display:block; font-size:12px; color:#555; margin-bottom:4px; }
.search-filters input[type="text"] { padding:8px 12px; border:1px solid #ccc; border-radius:6px; min-width:200px; }
.search-filters select { padding:8px 12px; border:1px solid #ccc; border-radius:6px; min-width:140px; background:white; }
.search-filters .filter-group { display:flex; flex-direction:column; }
.search-filters button.search-btn { background:#4CAF50; color:white; padding:8px 16px; border:none; border-radius:6px; cursor:pointer; }
.search-filters button.clear-btn { background:#757575; color:white; padding:8px 16px; border:none; border-radius:6px; cursor:pointer; }
.search-filters .filter-actions { display:flex; gap:8px; align-items:flex-end; }
</style>
</head>
<body>

<header>
    <h1>My Dashboard</h1>
    <button onclick="logout()">Logout</button>
</header>

<div class="container">
    <!-- Search & filters for bookmarks -->
    <div class="search-filters">
        <div class="row">
            <div class="filter-group">
                <label>Search (title / description)</label>
                <input type="text" id="search-input" placeholder="Search bookmarks..." onkeypress="if(event.key==='Enter')applyFilters()">
            </div>
            <div class="filter-group">
                <label>Favorite</label>
                <select id="filter-favorite">
                    <option value="">All</option>
                    <option value="1">Favorites only</option>
                    <option value="0">Not favorite</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Tag</label>
                <select id="filter-tag">
                    <option value="">All tags</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Collection</label>
                <select id="filter-collection">
                    <option value="">All collections</option>
                </select>
            </div>
            <div class="filter-actions">
                <button class="search-btn" onclick="applyFilters()">Search</button>
                <button class="clear-btn" onclick="clearFilters()">Clear</button>
            </div>
        </div>
    </div>

        <!-- Search Results -->
    <div id="search-results-container" 
    style="display:none; background:white; padding:15px; border-radius:8px; margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">

    <h3>Search Results</h3>
    <table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th>Title</th>
            <th>URL</th>
            <th>Description</th>
            <th>Tags</th>
            <th>Favorite</th>
        </tr>
    </thead>
    <tbody id="search-results"></tbody>
    </table>
    </div>

    <!-- Bookmarks -->
    <h2>Bookmarks <button class="add-btn" onclick="openBookmarkModal()">Add</button></h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>URL</th>
                <th>Description</th>
                <th>Tags</th>
                <th>Favorite</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="bookmarks"></tbody>
    </table>

    <!-- Collections -->
    <h2>Collections <button class="add-btn" onclick="openCollectionModal()">Add</button></h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>#Bookmarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="collections"></tbody>
    </table>

    <!-- Tags -->
    <h2>Tags</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>#Bookmarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tags"></tbody>
    </table>
</div>

<!-- Bookmark Modal -->
<div class="modal" id="bookmark-modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('bookmark-modal')">&times;</span>
        <h3 id="bookmark-modal-title">Add Bookmark</h3>
        <input type="text" id="bookmark-url" placeholder="URL">
        <input type="text" id="bookmark-title" placeholder="Title">
        <textarea id="bookmark-description" placeholder="Description"></textarea>
        <input type="text" id="bookmark-tags" placeholder="Tags comma separated">
        <label><input type="checkbox" id="bookmark-favorite"> Favorite</label>
        <button onclick="saveBookmark()">Save</button>
        <div class="message" id="bookmark-message"></div>
    </div>
</div>

<!-- Collection Modal -->
<div class="modal" id="collection-modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('collection-modal')">&times;</span>
        <h3 id="collection-modal-title">Add Collection</h3>
        <input type="text" id="collection-name" placeholder="Collection Name">
        <button onclick="saveCollection()">Save</button>
        <div class="message" id="collection-message"></div>
    </div>
</div>

<!-- Tag Modal -->
<div class="modal" id="tag-modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('tag-modal')">&times;</span>
        <h3 id="tag-modal-title">Add Tag</h3>
        <input type="text" id="tag-name" placeholder="Tag Name">
        <button onclick="saveTag()">Save</button>
        <div class="message" id="tag-message"></div>
    </div>
</div>

<!-- Add Bookmark to Collection Modal -->
<div class="modal" id="add-bookmark-collection-modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('add-bookmark-collection-modal')">&times;</span>
        <h3>Add Bookmark to Collection</h3>
        <select id="bookmark-select" style="width:100%; padding:5px; margin:10px 0;"></select>
        <button onclick="addSelectedBookmarkToCollection()">Add</button>
        <div class="message" id="add-bookmark-message"></div>
    </div>
</div>


<script>
const apiBase = '/api';
const token = localStorage.getItem('auth_token');
if(!token) window.location.href='/login';
const headers = { Authorization: 'Bearer ' + token };

let editingBookmarkId = null;
let editingCollectionId = null;
let editingTagId = null;

// Modal helpers
function openModal(id) { document.getElementById(id).style.display='flex'; }
function closeModal(id) { document.getElementById(id).style.display='none'; }

// --- Bookmarks (with optional search/filters) ---
function getBookmarkFilters() {
    const search = document.getElementById('search-input').value.trim();
    const favorite = document.getElementById('filter-favorite').value;
    const tag = document.getElementById('filter-tag').value;
    const collection = document.getElementById('filter-collection').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (favorite !== '') params.set('favorite', favorite);
    if (tag) params.set('tags', tag);
    if (collection) params.set('collection', collection);
    return params.toString();
}

function applyFilters() {
    const qs = getBookmarkFilters();
    const url = qs ? `${apiBase}/bookmarks?${qs}` : `${apiBase}/bookmarks`;

    axios.get(url, { headers })
    .then(res => {
        const container = document.getElementById('search-results');
        const wrapper = document.getElementById('search-results-container');

        container.innerHTML = '';

        if (!res.data.data.bookmarks.length) {
            container.innerHTML = '<tr><td colspan="5">No results found</td></tr>';
        } else {
            res.data.data.bookmarks.forEach(b => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${b.title}</td>
                    <td><a href="${b.url}" target="_blank">${b.url}</a></td>
                    <td>${b.description || ''}</td>
                    <td>${b.tags.map(t => t.name).join(', ')}</td>
                    <td>${b.is_favorite ? 'Yes' : 'No'}</td>
                `;
                container.appendChild(tr);
            });
        }

        wrapper.style.display = 'block';
    })
    .catch(err => console.error(err));
}


function fetchBookmarks() {
    const qs = getBookmarkFilters();
    const url = qs ? `${apiBase}/bookmarks?${qs}` : `${apiBase}/bookmarks`;
    axios.get(url, { headers })
    .then(res => {
        const container = document.getElementById('bookmarks');
        container.innerHTML = '';

        res.data.data.bookmarks.forEach(b => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>
                    <img src="${b.favicon_url || '/images/default-icon.png'}" 
                         alt="icon" 
                         style="width:20px; height:20px; margin-right:5px; vertical-align:middle;">
                    ${b.title}
                </td>
                <td><a href="${b.url}" target="_blank">${b.url}</a></td>
                <td>${b.description || ''}</td>
                <td>${b.tags.map(t => t.name).join(', ')}</td>
                <td>${b.is_favorite ? 'Yes' : 'No'}</td>
                <td>
                    <button onclick='openBookmarkModal(${JSON.stringify(b)})' class="edit">Edit</button>
                    <button onclick="deleteBookmark(${b.id})" class="delete">Delete</button>
                    <button onclick="toggleFavorite(${b.id})" class="favorite">⭐</button>
                </td>
            `;

            container.appendChild(tr);
        });
    })
    .catch(err => console.error(err));
}

function saveBookmark(){
    const data={ url: document.getElementById('bookmark-url').value.trim(),
                 title: document.getElementById('bookmark-title').value.trim(),
                 description: document.getElementById('bookmark-description').value.trim(),
                 tags: document.getElementById('bookmark-tags').value.split(',').map(t=>t.trim()).filter(t=>t),
                 is_favorite: document.getElementById('bookmark-favorite').checked };
    if(!data.url){ alert('URL required!'); return; }
    const req = editingBookmarkId
        ? axios.put(`${apiBase}/bookmarks/${editingBookmarkId}`, data, { headers })
        : axios.post(`${apiBase}/bookmarks`, data, { headers });
    req.then(()=>{ closeModal('bookmark-modal'); fetchBookmarks(); editingBookmarkId=null; })
       .catch(err=>{ console.error(err); alert(err.response?.data?.message||'Failed'); });
}
function deleteBookmark(id){ axios.delete(`${apiBase}/bookmarks/${id}`, { headers }).then(fetchBookmarks); }
function toggleFavorite(id){ axios.post(`${apiBase}/bookmarks/${id}/toggle-favorite`, {}, { headers }).then(fetchBookmarks); }

// --- Populate filter dropdowns (tags & collections) ---
function populateFilterDropdowns() {
    const tagSelect = document.getElementById('filter-tag');
    const collectionSelect = document.getElementById('filter-collection');
    const currentTag = tagSelect.value;
    const currentCollection = collectionSelect.value;
    tagSelect.innerHTML = '<option value="">All tags</option>';
    collectionSelect.innerHTML = '<option value="">All collections</option>';
    axios.get(`${apiBase}/tags`, { headers }).then(res => {
        res.data.data.tags.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.name;
            opt.textContent = t.name;
            if (t.name === currentTag) opt.selected = true;
            tagSelect.appendChild(opt);
        });
    }).catch(() => {});
    axios.get(`${apiBase}/collections`, { headers }).then(res => {
        res.data.data.collections.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.name;
            if (String(c.id) === currentCollection) opt.selected = true;
            collectionSelect.appendChild(opt);
        });
    }).catch(() => {});
}

// --- Collections ---
function fetchCollections() {
    axios.get(`${apiBase}/collections`, { headers })
    .then(res => {
        const container = document.getElementById('collections');
        container.innerHTML = '';

        res.data.data.collections.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${c.name}</td>
                <td>${c.bookmarks_count}</td>
                <td>
                    <button class="edit" onclick='openCollectionModalById(${c.id})'>Edit</button>
                    <button class="delete" onclick="deleteCollection(${c.id})">Delete</button>
                    <button onclick="toggleCollectionBookmarks(${c.id})">Show Bookmarks</button>
                </td>
            `;
            container.appendChild(tr);
        });

    })
    .catch(err => console.error(err));
}
function saveCollection(){
    const data={name: document.getElementById('collection-name').value};
    const req = editingCollectionId
        ? axios.put(`${apiBase}/collections/${editingCollectionId}`, data, { headers })
        : axios.post(`${apiBase}/collections`, data, { headers });
    req.then(()=>{ closeModal('collection-modal'); fetchCollections(); editingCollectionId=null; })
       .catch(err=>{ console.error(err); alert(err.response?.data?.message||'Failed'); });
}
function deleteCollection(id){ axios.delete(`${apiBase}/collections/${id}`, { headers }).then(fetchCollections); }
function openCollectionModalById(id){
    axios.get(`${apiBase}/collections/${id}`, { headers })
        .then(res => openCollectionModal(res.data.data.collection))
        .catch(err => console.error(err));
}
function removeBookmarkFromCollection(collectionId, bookmarkId){
    axios.delete(`${apiBase}/collections/${collectionId}/bookmarks/${bookmarkId}`, { headers })
        .then(() => {
            fetchCollections();
        })
        .catch(err => {
            console.error(err);
        });
}
function toggleCollectionBookmarks(id){
    const tr = document.getElementById(`collection-bookmarks-${id}`);
    tr.style.display = (tr.style.display==='none') ? '' : 'none';
}
function openAddBookmarkToCollection(collectionId){
    alert('هنا تقدر تعمل modal لإضافة bookmark موجود للـ Collection');
}

// --- Tags ---
function fetchTags(){
    axios.get(`${apiBase}/tags`, { headers })
    .then(res=>{
        const container = document.getElementById('tags');
        container.innerHTML = '';
        res.data.data.tags.forEach(t=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${t.name}</td>
                <td>${t.bookmarks_count}</td>
                <td>
                    <button class="edit" onclick='openTagModal(${JSON.stringify(t)})'>Edit</button>
                    <button class="delete" onclick="deleteTag(${t.id})">Delete</button>
                </td>`;
            container.appendChild(tr);
        });
    }).catch(err=>console.error(err));
}
function saveTag(){ const data={name: document.getElementById('tag-name').value};
    const req = editingTagId ? axios.put(`${apiBase}/tags/${editingTagId}`, data, { headers }) : axios.post(`${apiBase}/tags`, data, { headers });
    req.then(()=>{ closeModal('tag-modal'); fetchTags(); editingTagId=null; }).catch(err=>{ console.error(err); alert(err.response?.data?.message||'Failed'); });
}
function deleteTag(id){ axios.delete(`${apiBase}/tags/${id}`, { headers }).then(fetchTags); }

// Logout
function logout(){ axios.post(`${apiBase}/logout`, {}, { headers }).then(()=>{ localStorage.removeItem('auth_token'); window.location.href='/login'; }); }

// Modals
function openBookmarkModal(b=null){
    editingBookmarkId=b?.id||null;
    document.getElementById('bookmark-modal-title').textContent=b?'Edit Bookmark':'Add Bookmark';
    document.getElementById('bookmark-url').value=b?.url||'';
    document.getElementById('bookmark-title').value=b?.title||'';
    document.getElementById('bookmark-description').value=b?.description||'';
    document.getElementById('bookmark-tags').value=b?b.tags.map(t=>t.name).join(','):'';
    document.getElementById('bookmark-favorite').checked=b?.is_favorite||false;
    openModal('bookmark-modal');
}
function openCollectionModal(c=null){editingCollectionId=c?.id||null; document.getElementById('collection-modal-title').textContent=c?'Edit Collection':'Add Collection'; document.getElementById('collection-name').value=c?.name||''; openModal('collection-modal');}
function openTagModal(t=null){editingTagId=t?.id||null; document.getElementById('tag-modal-title').textContent=t?'Edit Tag':'Add Tag'; document.getElementById('tag-name').value=t?.name||''; openModal('tag-modal');}


let currentCollectionId = null; // لحفظ الـ Collection الحالي عند فتح الـ Modal

function openAddBookmarkToCollection(collectionId){
    currentCollectionId = collectionId;
    const select = document.getElementById('bookmark-select');
    select.innerHTML = '';
    
    // جلب كل Bookmarks الموجودة
    axios.get(`${apiBase}/bookmarks`, { headers })
        .then(res=>{
            res.data.data.bookmarks.forEach(b=>{
                const option = document.createElement('option');
                option.value = b.id;
                option.textContent = b.title + ' (' + b.url + ')';
                select.appendChild(option);
            });
            openModal('add-bookmark-collection-modal');
        }).catch(err=>{
            console.error(err);
            alert('Failed to load bookmarks');
        });
}

// بعد اختيار bookmark من الـ select
function addSelectedBookmarkToCollection(){
    const bookmarkId = document.getElementById('bookmark-select').value;
    if(!bookmarkId) return;

    axios.post(`${apiBase}/collections/${currentCollectionId}/bookmarks/${bookmarkId}`, {}, { headers })
        .then(res=>{
            closeModal('add-bookmark-collection-modal');
            fetchCollections(); // تحديث البيانات
            currentCollectionId = null;
        }).catch(err=>{
            console.error(err);
            document.getElementById('add-bookmark-message').textContent = err.response?.data?.message || 'Failed';
        });
}



// Initial load
fetchBookmarks();
fetchCollections();
fetchTags();
populateFilterDropdowns();

</script>

</body>
</html>
