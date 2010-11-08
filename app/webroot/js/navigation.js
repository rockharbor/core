/**
 * Js needed for the main navigation area
 */

CORE.initNavigation = function() {
	$('#nav-ministries .campuses input:radio').change(function() {
		$('#nav-ministries li[id^=campus]').hide();
		$('#nav-ministries li#campus-'+$(this).val()).show();
	});
	$('#nav-ministries .campuses input:radio:first').change();
}