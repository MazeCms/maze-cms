function setContentEditor(data)
{
    tinyMCE.activeEditor.execCommand("mceInsertContent", false, data);
}

function setContentImages(files, fm)
{
    if (files.search(/\.jpg|\.png|\.jpeg|\.gif$/gi) !== -1)
    {
        setContentEditor("<img src=\"" + files + "\"/>");
    }
    else
    {
        var title = files.match(/.+\/(.+)\.[a-z]{1,5}$/i);
        title = typeof title[1] !== undefined ? title[1] : title[0];
        setContentEditor("<a href=\"" + files + "\">" + title + "</a>");
    }
}