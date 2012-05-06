jQuery(function() {
	var $ = jQuery;
	
	var table = $('#contents table');
	var context = $('#context');
	
	table.find('thead th:eq(2)').after('<th>Status</th>');
	
	table.find('tbody tr').each(function() {
		$(this).find('td:eq(2)').after('<td class="status"></td>');
	});
	
	context.append('<ul class="actions"></ul>');
	context.find('ul.actions').append('<li><a href="#" class="button updates">Check For Updates</a></li>');
	
	context.find('a.updates').on('click', function(e) {
		e.preventDefault();
		
		table.find('tbody tr').each(function() {
			var row = $(this);
			var status = row.find('td.status');
			status.empty();
			
			var id = row.find('input[type="checkbox"]').attr('name').replace(/(items\[)([a-z0-9-_]+)(\])/gi, '$2');
			
			$.ajax({
				url: '/symphony/extension/extension_status/proxy/?id=' + id,
				dataType: 'xml',
				success: function(response) {
					response = $(response).find('response');
					if(response.attr('error') == '404') {
						status.text('Not found');
					}
					else if(response.attr('compatible-version-exists') == 'yes') {
						status.html('Compatible; use <a href="' + response.attr('latest-url') + '">' + response.attr('latest') + "</a>");
					} else {
						status.text('Not compatible with ' + response.attr('symphony-version'));
					}
				}
			})

		});
		
	});
	
});