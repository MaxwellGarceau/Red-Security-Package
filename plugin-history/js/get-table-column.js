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
              columnText = columnText + '\r' + rangeObj.toString();
          });


      }
      else { // Internet Explorer before version 9
          cells.each(function(i, cell) {
              var rangeObj = document.body.createTextRange();
              rangeObj.moveToElementText(cell);
              rangeObj.select();
              columnText = columnText + '\r' + rangeObj.toString();
          });
      }

      // navigator.clipboard.writeText(columnText);
      prompt('Copy text.', columnText.trim());
  }


  $(document).ready(function() {
      $('#plugin-history thead th').each(function(index) {
          $(this).click(function() {
              SelectColumn(index, 'plugin-history');
          });
      });
  });
})(jQuery);
