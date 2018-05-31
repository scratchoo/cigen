$(document).ready(function()
{
    $("[data-method=delete]").on('click', function(e)
    {
        e.preventDefault();
        e.stopPropagation();

        if($(this)[0].hasAttribute("data-confirm"))
        {
            if(!confirm($(this).attr('data-confirm')))
            {
                return false;
            }
        }

        var href = $(this).attr('href');
        var csrf_token =  $('meta[name=csrf-token]').attr('content');
        var csrf_name = $('meta[name=csrf-token]').attr('name');
        var form = document.createElement('form');
        var formContent = "<input name='" + csrf_name + "' value='" + csrf_token + "' type='hidden' />";
        formContent += '<input name="_method" value="delete" type="hidden"/>';
        formContent += '<input type="submit" />';

        form.method = 'post';
        form.action = href;
        form.target = $(this).target || '_self';
        form.innerHTML = formContent;
        form.style.display = 'none';

        document.body.appendChild(form);
        form.querySelector('[type="submit"]').click();

    });
});
