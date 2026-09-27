$(function () {
    const makeSlug = (value) => value.trim().toLowerCase().normalize('NFKD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '') || 'category';
    $('#create-category-name').on('input', function () { $('#create-category-slug').val(makeSlug(this.value)); });
    $('#create-category-name').trigger('input');
    $('.category-edit-form').each(function () {
        const $form = $(this);
        const $name = $form.find('[name="name"]');
        $name.on('input', function () { $form.find('.category-slug-preview').val(makeSlug(this.value)); });
        $name.trigger('input');
    });
    $('#newCategoryName').on('input', function () { $('#newCategorySlug').val(makeSlug(this.value)); });

    $('#toggleCategoryPicker').on('click', function () {
        const collapsed = $(this).attr('aria-expanded') === 'false';
        $(this).attr('aria-expanded', String(!collapsed)).attr('aria-label', collapsed ? 'Collapse categories' : 'Expand categories');
        $(this).find('i').toggleClass('bi-chevron-down', !collapsed).toggleClass('bi-chevron-up', collapsed);
        $('.category-picker > :not(.field-error)').toggle(collapsed);
    });

    $('[data-admin-sidebar-toggle]').on('click', function () {
        $('body').toggleClass('admin-sidebar-open');
    });

    $('.admin-main').on('click', function (event) {
        if ($(window).width() < 992 && $('body').hasClass('admin-sidebar-open') && !$(event.target).closest('.admin-sidebar, [data-admin-sidebar-toggle]').length) {
            $('body').removeClass('admin-sidebar-open');
        }
    });

    $(document).on('submit', 'form[data-confirm]', function (event) {
        if (!window.confirm($(this).data('confirm'))) event.preventDefault();
    });

    const previewUrls = [];
    $('#images').on('change', function () {
        previewUrls.forEach((url) => URL.revokeObjectURL(url));
        previewUrls.length = 0;
        const $preview = $('#imagePreview').empty();

        Array.from(this.files || []).slice(0, 12).forEach((file) => {
            const url = URL.createObjectURL(file);
            previewUrls.push(url);
            const $item = $('<div>', { class: 'upload-preview-item' });
            $('<img>', { src: url, alt: '' }).appendTo($item);
            $('<span>').text(file.name).appendTo($item);
            $preview.append($item);
        });
    });

    const $categoryModal = $('#addCategoryModal');
    const $saveCategory = $('#saveCategory');
    if ($categoryModal.length && $saveCategory.length) {
        $saveCategory.on('click', function () {
            const $name = $('#newCategoryName');
            const $description = $('#newCategoryDescription');
            const $slug = $('#newCategorySlug');
            const parentId = $('#newCategoryParents').val() || '';
            const $error = $('#categoryError').empty();
            const $success = $('#categorySuccess').empty();
            const name = $.trim($name.val());
            if (!name) {
                $error.text('Enter a category name first.');
                $name.trigger('focus');
                return;
            }

            const originalText = $saveCategory.text();
            $saveCategory.prop('disabled', true).text('Adding…');
            $.ajax({
                url: $categoryModal.data('category-create-url'),
                method: 'POST',
                data: { name, slug: $.trim($slug.val()), description: $.trim($description.val()), parent_id: parentId },
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            }).done((result) => {
                const $choice = $('<label>', { class: 'category-tree-item', 'data-category-name': result.path.toLowerCase() }).css('--tree-depth', result.depth);
                $('<input>', { class: 'form-check-input category-choice', type: 'checkbox', 'data-category-id': result.id, checked: true }).appendTo($choice);
                $('<span>').text(result.path).appendTo($choice);
                $('#categoryTreeList').append($choice);
                $('#newCategoryParents').append(new Option(result.path, result.id));
                $success.text('Category added and selected.');
                $name.val('');
                $description.val('');
                window.setTimeout(() => bootstrap.Modal.getOrCreateInstance($categoryModal[0]).hide(), 450);
            }).fail((xhr) => {
                const messages = Object.values(xhr.responseJSON?.errors || {}).flat();
                $error.text(messages[0] || xhr.responseJSON?.message || 'Could not add that category.');
            }).always(() => $saveCategory.prop('disabled', false).text(originalText));
        });

        $categoryModal.on('hidden.bs.modal', function () {
            $('#categoryError, #categorySuccess').empty();
            $('#newCategoryName, #newCategorySlug, #newCategoryDescription').val('');
            $('#newCategoryParents').val('');
        });
    }

    $(document).on('change', '.category-choice', function () {
        const categoryId = String($(this).data('category-id'));
        $('.category-choice').filter(function () { return String($(this).data('category-id')) === categoryId; }).prop('checked', this.checked);
    });
    $('#categorySearch').on('input', function () {
        const query = $.trim(this.value).toLowerCase();
        $('.category-tree-item').each(function () { $(this).toggle(!query || $(this).data('category-name').includes(query)); });
    });
    $('#storyForm').on('submit', function () {
        $(this).find('.category-submit-value').remove();
        const selected = new Set();
        $('.category-choice:checked').each(function () { selected.add(String($(this).data('category-id'))); });
        selected.forEach((id) => $('<input>', { class: 'category-submit-value', type: 'hidden', name: 'category_ids[]', value: id }).appendTo('#storyForm'));
    });
});
