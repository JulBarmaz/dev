jQuery.fn.bbcode_editor = function(option) {
    option = jQuery.extend({
        tag_p: true,
        tag_br: true,
        tag_b: true,
        tag_i: true,
        tag_s: true,
        tag_u: true,
        tag_url: true,
        tag_img: true,
        tag_size: true,
        tag_color: true,
        tag_ul: true,
        tag_ol: true,
        tag_code: true,
        tag_h1: true,
        tag_h2: true,
        tag_h3: true,
        tag_h4: true,
        tag_h5: true,
        tag_h6: true,
        separator: ['br', 'u','img','color','ol','code'],
        move_caret: true
    }, option);
    return this.each(function() {
        tags = [[option.tag_p, 'p', '[p]', '[/p]',  false],
            [option.tag_br, 'br', '[br]', '',  false],
            [option.tag_b, 'b', '[b]', '[/b]',  false],
            [option.tag_i, 'i', '[i]', '[/i]', false],
            [option.tag_s, 's', '[s]', '[/s]', false],
            [option.tag_u, 'u', '[u]', '[/u]', false],
            [option.tag_url, 'url', '[url=', '[/url]', true],
            [option.tag_img, 'img', '[img]', '[/img]', false],
            [option.tag_size, 'size', '[size=', '[/size]', true],
            [option.tag_color, 'color', '[color=', '[/color]', true],
            [option.tag_ul, 'ul', "[list][*]", "[/list]", false],
            [option.tag_ol, 'ol', "[list=1][*]", "[/list]", false],
            [option.tag_code, 'code', '[code]', '[/code]', false],
            [option.tag_h1, 'h1', '[h1]', '[/h1]',  false],
            [option.tag_h2, 'h2', '[h2]', '[/h2]',  false],
            [option.tag_h3, 'h3', '[h3]', '[/h3]',  false],
            [option.tag_h4, 'h4', '[h4]', '[/h4]',  false],
            [option.tag_h5, 'h5', '[h5]', '[/h5]',  false],
            [option.tag_h6, 'h6', '[h6]', '[/h6]',  false]];
        var id = $(this).attr("id");
        var bbeditor = $('#' + id);
        bbcode_editor_bar = "<div class='bbcode_editor_bar col-md-12'><ul>";
        tags_len = tags.length;
        for (i = 0;i < tags_len;i++) {
            if (tags[i][0]) {
                if (tags[i][4]) {
                    click = "ta=$(\"#"+id+"\").get(0);txt=prompt(\""+tags[i][1]+"\");if(txt!=null){insert(ta,{open_tag:\""+tags[i][2]+"\"+txt+\"]\",close_tag:\""+tags[i][3]+"\",move_caret:"+option.move_caret+"});}return false;";
                    bbcode_editor_bar += "<li><a onclick='" + click + "' title='" + tags[i][2] + ']' + tags[i][3] + "' href='#' id='#" + tags[i][1] + id + "' class='" + tags[i][1] + "'><i>&nbsp;&nbsp;&nbsp;&nbsp;</i></a></li>";
                }else {
                    bbcode_editor_bar += "<li><a onclick='insert($(\"#" + id + "\").get(0),{open_tag:\"" + tags[i][2] + "\",close_tag:\"" + tags[i][3]  + "\",move_caret:"+option.move_caret+"});return false;' title='" + tags[i][2] + tags[i][3] + "' href='#' id='#" + tags[i][1] + id + "' class='" + tags[i][1] + "'><i>&nbsp;&nbsp;&nbsp;&nbsp;</i></a></li>";
                }
            }
            if (option.separator) {
                sep_len = option.separator.length;
                for (j = 0;j < sep_len;j++) {
                    if(option.separator[j] == tags[i][1]) {
                        bbcode_editor_bar += "<li><a class='separator' href='#'><b></b></a></li>";
                    }
                };
            }
        };
        bbcode_editor_bar += '</ul></div>';
        bbeditor.wrap('<div class="bb_editor_main row"></div>');
        bbeditor.before(bbcode_editor_bar);
        bbeditor.wrap('<div class="col-md-12"></div>');
    });
};
function insert (textarea, option) {
        option = jQuery.extend({
        open_tag: '',
        close_tag: '',
        move_caret: true
    }, option);
    if ('selectionStart' in textarea) {
        var startPos = textarea.selectionStart;
        var endPos = textarea.selectionEnd;
        sel = option.open_tag + textarea.value.substring(startPos, endPos) + option.close_tag;
        newPos = startPos + sel.length - option.close_tag.length;
        textarea.value = textarea.value.substring(0, startPos) + sel + textarea.value.substring(endPos, textarea.value.length);
        if (option.move_caret) {
            textarea.setSelectionRange(newPos, newPos);
        }
        textarea.focus();
    }else if (document.selection) {
        textarea.focus();
        sel = document.selection.createRange();
        sel.text = option.open_tag + sel.text + option.close_tag;
        if (option.move_caret) {
            sel.collapse(true);
            newPos = sel.text.length - option.close_tag.length;
            sel.moveStart('character', newPos);
            sel.moveEnd('character', newPos);
            sel.select();
        }
        textarea.focus();
    }else {
        textarea.value += option.open_tag + textarea.value + option.close_tag;
        textarea.focus();
    }
}