(function ($) {
  // Source: http://jsfiddle.net/4BwGG/3/
  function SelectColumn(index, tableId) {
    var columnText = '';
    var columnSelector = '#' + tableId + ' tbody > tr > td:nth-child(' + (index + 1) + ')';
    var cells = $(columnSelector);

    // clear existing selections
    if (window.getSelection) { // all browsers, except IE before version 9
      window.getSelection().removeAllRanges();
    }

    if (document.createRange) {
      cells.each(function(i, cell) {
        var rangeObj = document.createRange();
        rangeObj.selectNodeContents(cell);
        window.getSelection().addRange(rangeObj);

        if (rangeObj.toString()) {
          columnText = columnText + '\r' + rangeObj.toString();
        }

      });

    }
    return columnText.trim();
  }

  // Source: https://www.30secondsofcode.org/blog/s/copy-text-to-clipboard-with-javascript
  function copyToClipboard(str) {
    var el = document.createElement('textarea');
    el.value = str;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
  };


  $(document).ready(function() {
    $('#plugin-history thead th').each(function(index) {
      $(this).click(function(e) {
        var columnText = SelectColumn(index, 'plugin-history');
        copyToClipboard(columnText);
        alert('Text successfully copied!' + '\r\n\r\n' + columnText);
      });
    });
  });
})(jQuery);
