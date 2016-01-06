<?php
/* Generate a prof's page from input ID (profs name, no commas and no spaces) */

// Load config
include("config.php");


//Connect to MySQL
$db = new database;
//Get data
$list = $db->pdo->prepare("SELECT  data.Course, data.courseID, ROUND(AVG(data.GPA),2) as GPA, COUNT(*) as rcount, ROUND(AVG(data.A)) as A, ROUND(AVG(data.B)) as B, ROUND(AVG(data.C)) as C, ROUND(AVG(data.D)) as D, ROUND(AVG(data.F)) as F,  ROUND(AVG(data.W)) as W
											FROM data 
											INNER JOIN socsci on data.courseID=socsci.courseID
											WHERE GPA != 0
											GROUP BY data.Course
											ORDER BY GPA DESC");

//execute query and handle error
if (!$list->execute() ) {
    $error = file_get_contents("404.html");
    die($error);
}

// Get the professor's name
$courseList = $list->fetchAll(PDO::FETCH_OBJ);


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Course Critique - Social Sciences</title>
        <meta name="description" content="Historical Course GPA information provided by SGA">
        <meta name="author" content="SGA - Georgia Institute of Technology">

        <!--[if lt IE 9]>
          <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="default/bootstrap-responsive.min.css" rel="stylesheet"> 
        <link href="css/bootswatch.css" rel="stylesheet">
        <link href="css/critique.css" rel="stylesheet">
        <script src="js/jquery.min.js"></script>
        
        <!-- DataTables -->
        <script src="js/dataTables/jquery.dataTables.js"></script>
        <script src="js/dataTables/DT_bootstrap.js"></script>
        <link href="css/dataTables/DT_bootstrap.css" rel="stylesheet" >
    </head>

    <body>
        <img src="img/beta_ribbon.png" class="beta-ribbon" alt="beta" />
        <div class="container">
			<h1>Social Sciences</h1>
			<button type="button" id="nextOnly" class="btn btn-primary" onclick="nextSem();">Classes Offered Next Semester</button>
            <!-- Table -->
            <table class="table table-striped table-bordered" id="dataTable" style="margin-top: 10px;" >
                <thead>
                    <tr class="table-head">
						<th>Course</th>
						<th>GPA</th>
						<th>Num Courses</th>
						<th>A%</th>
						<th>B%</th>
						<th>C%</th>
						<th>D%</th>
						<th>F%</th>
						<th>W%</th>
					</tr>
                </thead>
                <tbody id="fullTable">
				
                </tbody>
            </table>

            <!-- javascript placed at end of the document so the pages load faster -->
            <script src="js/bootstrap.min.js"></script>
            <script src="js/application.js"></script>
					<!-- Course Data from PHP -->
            <script>
                
                //Generate dropdown navigation filters
				var courses = <?php echo json_encode($courseList); ?>;
				var count = courses.length;
				$.each(courses, function(index, elem){
						$("#fullTable").append("<tr id="+elem.courseID+"><td>"+elem.Course+"</td><td>"+elem.GPA+"</td><td>"+elem.rcount+"</td><td>"+elem.A+"</td><td>"+elem.B+"</td><td>"+elem.C+"</td><td>"+elem.D+"</td><td>"+elem.F+"</td><td>"+elem.W+"</td></tr>");
						//if (!--count) initTable();
				});
				
				var nextSemester;
				$.get( "https://soc.courseoff.com/gatech/terms", function( data ) {
					nextSemester = data[data.length - 1].ident;
				}, "json" );


				function nextSem(){
					$.each(courses, function(index, elem){
						//console.log(elem);
						//console.log("https://soc.courseoff.com/gatech/terms/"+nextSemester+"/majors/"+elem.Course.split(" ")[0]+"/courses/"+elem.Course.split(" ")[1] );
						$.ajax({ 
							url: "https://soc.courseoff.com/gatech/terms/"+nextSemester+"/majors/"+elem.Course.split(" ")[0]+"/courses/"+elem.Course.split(" ")[1],
							type: 'get',
							success: function( data ) {
											if(!data){
												console.log("remove");
												$("#"+elem.courseID).hide();
											}
										}, 
							error: function(XMLHttpRequest, textStatus, errorThrown){
											$("#"+elem.courseID).hide();
										}
						});
					
						
						
					});

				};

                function genList(outID, elements) {
                    var listOptions = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">' + outID + ' <b class="caret"></b></a><ul id="' + outID + '" class="dropdown-menu">';
                    //Sort keys of hashtable
                    var keys = [];
                    for (var key in elements) {
                        if (elements.hasOwnProperty(key)) {
                            keys.push(key);
                        }
                    }
                    keys.sort();
                    for (var i in keys) {
                        listOptions += '<li><label for=\'' + keys[i] + '\'><input id=\'' + keys[i] + '\' type="checkbox" onclick="toggleAction(\'' + keys[i] + '\', \'' + outID + '\');" checked="true" />' + elements[keys[i]] + '</label></li>';
                    }
                    listOptions += "</ul></li>";
                    $("div#dataTable_wrapper > div.row > div.span6:first > ul").append(listOptions);
                }

                function toggleAction(itemClass, menuName) {
                    $("." + itemClass).toggleClass(menuName + "Disabled");
                }
                function urlhashFilter() {
                    /* Get first string in URL hash */
                    var url = location.hash.substring(1).split(' ')[0];
                    if (url.length === 0) {
                        return false;
                    }

                    /* Change to gold */
                    $("a.dropdown-toggle:first").toggleClass("prof-flash-gold");

                    /* Find the request prof in our filter list */
                    if ($("body").find("ul#Courses li input#" + url + ":first").length === 1) {
                        $("ul#Courses li input").each(function() {
                            /* Hide everything but the requested course */
                            if ($(this).attr('id') !== url) {
                                $(this).trigger('click');
                            }
                        });
                        dataTable.fnSort([[2, 'desc']]); //Sort Year asc
                    }
                    setTimeout(function() {
                        $("a.dropdown-toggle:first").toggleClass("prof-flash-gold");
                    }, 250);
                }

                //prevent dropdown from closing upon selection
                $(function() {
                    $('.dropdown input, .dropdown label').click(function(e) {
                        e.stopPropagation();
                    });
                });
            </script>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>
