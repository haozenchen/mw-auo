/**
 * set page break on the fly
 * @param int pbn Page break number
 */
function setPageBreak(pbn) {
	var rcnt=0;
	pbn = parseInt(pbn);
	if (pbn == 0) {
		pbn = 30;
	}
	$$('tr').each(function(e) {
       		if (e.parentNode.tagName != 'TBODY') {
			// if it's not in tbody, we won't care
			// thus we can avoid wrong counting on header tr
			return;
		}
		rcnt++;
		if (rcnt % pbn == 0) {
			e.style.pageBreakAfter = 'always';
		} else {
			e.style.pageBreakAfter = '';
		}
	});
}
