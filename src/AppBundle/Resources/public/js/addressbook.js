$(document).ready(function () {
    $('#listContainer').on('click', '.viewAddressBook', function () {
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function (res) {
                $('#viewAddressBookContainer').html(res);
                $('#viewAddressBookModal').modal('show')
            }
        })
        return false
    })
    $('#listContainer').on('click', '.deleteAddressBook', function () {
        var url = $(this).attr('href');
        $.bsAlert.confirm("Are You Sure?", function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (res) {
                    window.location.reload()
                }
            })
        });
        return false
    })
    var getList = function (url) {
        $.ajax({
            url: url,
            success: function (res) {
                $('#listContainer').html(res);
            }
        })
    }
    getList($('#firstPageUrl').val());
    $('#listContainer').on('click', '.pagination a , th a', function () {
        getList($(this).attr('href'));
        return false;
    })
    $('#searchForm').submit(function () {
        getList($(this).attr('action')+'?'+$(this).serialize());

        return false;
    })
})