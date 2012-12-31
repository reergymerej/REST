var REST = {};

$(function(){

	var url = 'api/';
	
	$('#restButtons button')
		.button()
		.click(function(){

			var noun = $.trim( $('#noun').val() ),
				id = $.trim( $('#id').val() ),
				fieldData = {};

			fieldData = getFieldData();

			if(noun === ''){
				showMessage('Please enter a noun.');
				$('#noun').focus();
				return;
			};

			$('#response').attr('disabled', 'disabled');

			switch( $(this).attr('id') ){
				case 'get':
					$.get(url + noun + '/' + id, function(resp){
						showResponse(resp);
					});

					break;

				case 'post':
					$.post(url + noun, fieldData, function(resp){
						showResponse(resp);
					});
					break;

				case 'put':
					$.ajax({
						url: url + noun + '/' + id,
						type: 'PUT',
						data: fieldData,
						success: function(resp){
							showResponse(resp);
						}
					});

					break;

				case 'delete':
					$.ajax({
						url: url + noun + '/' + id,
						type: 'DELETE',
						success: function(resp){
							showResponse(resp);
						}
					});

					break;
			};

			/**
			* Returns an object containing the fields/values to POST/PUT.
			* @return {object}
			**/
			function getFieldData(){
				var data = {};

				$('.field').each(function(){
					var name = $('.name', this).val(),
						value = $('.value', this).val();

					data[name] = value;
				});
				
				return data;
			};
		});

	$('#newField')
		.click(function(){
			new REST.FieldView();
			return false;
		});

	/**
	* @param {string} resp
	**/
	function showResponse(resp){
		$('#response')
			.empty()
			.html(resp)
			.removeAttr('disabled');
	};

	/**
	* @param {string} msg
	**/
	function showMessage(msg){
		var node = $('#message');

		if(node.is(':visible')){
			node
				.clearQueue()
				.delay(2000)
				.slideToggle();
			return;
		};

		node
			.empty()
			.html( msg )
			.slideToggle(function(){
				node
					.delay(2000)
					.slideToggle();
			});		
	};
});

REST.FieldView = Backbone.View.extend({
	initialize: function(){
		this.render();
	},
	render: function(){
		var template = _.template( $('#field_template').html(), {} );
		this.$el
			.addClass('field')
			.html( template );

		$('#fields').append( this.$el );
			
		this.$el
			.find('input:first')
			.focus();

		$('.delete', this.$el).hover(
			function(){ $( this ).addClass( "ui-state-hover" ); },
			function(){ $( this ).removeClass( "ui-state-hover" ); }
		);
	},
	events: {
		'click .delete': 'deleteField'
	},
	deleteField: function(){
		this.remove();
	}
});