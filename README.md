##  Mô tả chi tiết cho dự án Laravel Media Manager — công cụ xử lý ảnh, phục vụ cho các hệ thống CMS, blog, e-commerce, hoặc các hệ thống cần upload/quản lý ảnh số lượng lớn.

##  1.  Mục Tiêu Dự Án
    Tạo ra một Laravel Media Manager giúp:
    -   Quản lý ảnh theo user, folder
    -   Quản lý metadata ảnh (alt, title, mô tả)
    -   Upload, resize, crop, watermark ảnh
    -   Tạo thumbnail tự động
    -   API nội bộ để tái sử dụng trên frontend (Vue/React)
    -   Hỗ trợ tương thích cho Laravel Package


##  2. Tính Năng Chính
    Quản lý
    -   Thư mục (folder): Tạo, đổi tên, xóa, di chuyển, nén thành file
    -   Ảnh: Upload, download, đổi tên, xóa, di chuyển
    -   Tìm kiếm theo: tên, thẻ tag, định dạng
    -   Drag & drop upload
    -   Hỗ trợ upload nhiều định dạng: JPG, PNG, WEBP, SVG, PDF

    Xử lý ảnh (Image Processing)
    -   Resize ảnh tự động theo config
    -   Crop ảnh theo tỷ lệ hoặc custom
    -   Thêm watermark logo/text
    -   Tạo thumbnail (nhiều kích cỡ: small, medium, large)
    -   Chuyển đổi định dạng ảnh (JPG ⇄ PNG ⇄ WEBP)
    -   Auto-optimize ảnh khi upload (nén, strip metadata)

    Metadata & Tag
    -   Nhập các thông tin ảnh: alt, title, caption, description, keywords
    -   Gắn tag để tìm kiếm nhanh

    Tích hợp WYSIWYG
    -   Tích hợp với TinyMCE, CKEditor
    -   Tạo nút "Chọn ảnh từ Media Manager" để insert ảnh

    API nội bộ
    -   Endpoint để lấy ảnh (có phân trang, tìm kiếm)
    -   Endpoint upload ảnh từ frontend
    -   Trả về đường dẫn ảnh sau khi xử lý xong

    Phân quyền
    -   Chỉ admin được xóa ảnh gốc
    -   Người dùng thường chỉ xem ảnh mình upload
    -   Gán role/permission bằng Laravel Permission


##  3.  Cấu Trúc Cơ Bản
    Config (config/media-manager.php)
    -   Max file size
    -   Các kích cỡ thumbnail
    -   Logo watermark path
    -   Đường dẫn upload
    -   Storage disk (local, s3)


##  4. Kiến Trúc Database
    Bảng	                Mô tả
    users                   Có sẵn trong Laravel – liên kết media theo người dùng
    media_folders	        Thư mục quản lý
    media_files	            Lưu thông tin ảnh
    media_tags	            Tag gắn cho ảnh
    media_metadata          Metadata dạng động: width, height, exif, ...
    media_file_tag	        Pivot table (ảnh - tag)


                    Bảng media_files
    Tên cột	            Kiểu	                Ghi chú
    id	                bigIncrements
    user_id             foreignId               Người upload
    media_folder_id     foreignId	            Folder liên kết
    filename            string                  Tên file
    original_name       string                  Tên file gốc
    mime_type           string	                MIME type
    size                integer	                Kích cỡ file (bytes)
    path	            string	                Đường dẫn file
    thumbnail_path      string                  Đường dẫn thumbnail file
    source_url          string                  Đường dẫn nơi upload file
    is_public           bool                    Có công khai không?
    created_at	        timestamp
    updated_at	        timestamp


                    Bảng media_folders
    Tên cột	            Kiểu	                Ghi chú
    id	                bigIncrements
    user_id             foreignId               Người tạo
    name	            string	                Tên thư mục
    path                string                  Đường dẫn folder
    storage             string                  Kho lưu trữ
    parent_id	        nullable foreignId	    Cho phép nested folder
    created_at	        timestamp
    updated_at	        timestamp


                    Bảng media_tags
    Tên cột	            Kiểu	                Ghi chú
    id	                bigIncrements
    name	            string	                Tên thư mục
    created_at	        timestamp
    updated_at	        timestamp


                    Bảng media_file_tag
    Tên cột	            Kiểu	                Ghi chú
    id	                bigIncrements
    media_file_id	    foreignId	            File liên kết
    media_tag_id	    foreignId	            Tag liên kết
    created_at	        timestamp
    updated_at	        timestamp


                    Bảng media_metadata
    Tên cột	            Kiểu	                Ghi chú
    id	                bigIncrements
    media_file_id	    foreignId	            File liên kết
    key	                string	                Tên metadata
    value	            text	                Giá trị metadata tương ứng
    created_at	        timestamp
    updated_at	        timestamp


##  5. Luồng Xử Lý Ảnh / Upload Ảnh / Dowload Ảnh
    (Luồng xử lý chung)
    Người dùng tương tác (upload, xem, tải về, thao tác ảnh)
    ->  Laravel nhận request & validate (ảnh, quyền, kích thước,...)
    ->  Laravel xử lý ảnh với Intervention Image:
        -   Resize (nếu vượt kích thước cho phép)
        -   Tạo thumbnail
        -   Thêm watermark (nếu có)
        -   Đổi định dạng (nếu cần)
    ->  Lưu ảnh & thumbnail vào storage:
        -   storage/app/public/media/
        -   storage/app/public/media/thumbs/
    ->  Lưu metadata vào database:
        - Tên file, đường dẫn, user_id, dung lượng, loại ảnh,...
    ->  Trả về kết quả: URL ảnh, URL thumbnail, ID ảnh, Metadata khác
    ->  (Tùy chọn) Các chức năng khác:
        - Download: lấy file từ Storage, stream về
        - Xóa ảnh: xóa file vật lý + record DB
        - Cập nhật watermark hoặc thay thế

    (Luồng Upload)
    Người dùng upload ảnh
    ->  Ảnh lưu vào storage/app/public/media/
    ->  Laravel dùng Intervention Image để:
        -   Resize ảnh theo cấu hình (ví dụ: 1280px max width)
        -   Tạo thumbnail (ví dụ: 300x200)
        -   (Tùy chọn) Thêm watermark (logo, text, ...)
    ->  Ghi metadata vào database (filename, size, mime, user_id, ...)
    ->  Trả về response: ID, URL, Thumbnail URL, Metadata khác

    (Luồng Download)
    Người dùng yêu cầu tải ảnh (bằng URL hoặc nút tải về)
    ->  Laravel nhận request
    ->  Xác thực quyền truy cập (nếu có)
    ->  Kiểm tra file tồn tại trong Storage
    ->  (Tùy chọn) Xử lý ảnh động (resize, watermark, ...)
    ->  Trả file về response dạng stream download
    ->  (Tùy chọn) Ghi log tải ảnh (user, thời gian, ...)


##  6.  Giao Diện Quản Lý (Admin UI)
    -   Framework: Laravel
    -   Tính năng:
        -   Sidebar cây thư mục (breadcrumb)
        -   Grid ảnh, hiển thị thumbnail
        -   Modal upload ảnh
        -   Form chỉnh sửa metadata
        -   Kéo-thả di chuyển ảnh
        -   Filter theo tên, tag, folder, storage, người upload, thời gian


##  7. Phân Quyền (Role/Permission)
    Quyền	            Mô tả
    view	            Xem
    edit	            Sửa
    delete	            Xóa
    upload	            Upload
    manage              Tạo / Sửa / Xóa
    all                 Tất cả quyền

    file.*	            Có quyền gì với File?
    folder.*	        Có quyền gì với Folder?

    =>  Dùng package spatie/laravel-permission


##  8. Dự định Mở rộng
    -   Viết package
    -   Quản lý video/audio
    -   Hỗ trợ CDN (Cloudflare, BunnyCDN)
    -   Tích hợp AI nén ảnh (TinyPNG API, Spatie Image Optimizer)
    -   Nhận diện ảnh trùng
    -   Export file zip các ảnh chọn


##  9. Các Package hỗ trợ
    Package	                            Mục đích
    intervention/image	                Resize, crop ảnh
    spatie/laravel-medialibrary	        Quản lý media nâng cao
    spatie/image-optimizer	            Nén ảnh
    spatie/laravel-permission	        Phân quyền
    barryvdh/laravel-elfinder	        UI Media Manager (nếu muốn có sẵn)
    tailwindcss                         CSS Framework


##  10. Testing
    -   Test crud folder theo breadcrumb
    -   Test move folder
    -   Test upload ảnh
    -   Test move ảnh
    -   Test API resize, delete
    -   Test quyền người dùng
    -   Test filter

##  11. Tên sử dụng
    Thành phần	            Tên
    Package	                media-manager
    Namespace	            MyCompany\MediaManager
    Route prefix	        /admin/media
    Model	                User, MediaFile, MediaFolder, MediaTag, MediaMetadata
