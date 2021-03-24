<?php
defined('MOODLE_INTERNAL') || die();
GLOBAL $USER;
$now = time();
if(optional_param('id', 0, PARAM_INT)){
    $id = optional_param('id', 0, PARAM_INT);
} else {
    $id = optional_param('cmid', 0, PARAM_INT);
}

if (isset($id)){
    $starttime = get_config('block_teacher_report', "activity_starttime_".$USER->id."_".$id);

    if(!$starttime){
        set_config("activity_starttime_".$USER->id."_".$id, $now, 'block_teacher_report');
    }
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://serkanyersen.github.io/ifvisible.js/ifvisible.js"></script>
    <script>

    function d(el){
        return document.getElementById(el);
    }
    ifvisible.setIdleDuration(30);

    ifvisible.on('statusChanged', function(e){
        
    });

    ifvisible.idle(function(){
        
        document.body.style.opacity = 0.5;
    });

    ifvisible.wakeup(function(){
        console.log("ami achi1");
        document.body.style.opacity = 1;
    });

    ifvisible.onEvery(10, function(){
        // Clock, as simple as it gets
        var h = (new Date()).getHours();
        var m = (new Date()).getMinutes();
        var s = (new Date()).getSeconds();
        m = m < 10? "0"+m : m;
        s = s < 10? "0"+s : s;

        // Update clock
        
        console.log("ami achi");
        $.ajax({
            url: "<?php echo $CFG->wwwroot?>/blocks/teacher_report/engine.php",
            data:{'activity':<?php echo $id ?>, 'timespent': 10},
            type:'POST',
            success:function(response) {
              var resp = $.trim(response);
              console.log(resp); 
            }
        });
    });

    setInterval(function(){
        var info = ifvisible.getIdleInfo();
        // Give 3% margin to stabilaze user output
        if(info.timeLeftPer < 3){
            info.timeLeftPer = 0;
            info.timeLeft = ifvisible.getIdleDuration();
        }
        
    }, 100);
    </script>

<?php 
}
include($CFG->dirroot . '/theme/edumy/ccn/ccn_themehandler.php');
if ($incourse_layout_dashboard == 1) {
  array_push($extraclasses, "ccn_context_dashboard");
  $bodyclasses = implode(" ",$extraclasses);
  $bodyattributes = $OUTPUT->body_attributes($bodyclasses);
  include($CFG->dirroot . '/theme/edumy/ccn/ccn_themehandler_context.php');
  echo $OUTPUT->render_from_template('theme_edumy/ccn_dashboard', $templatecontext);
} elseif ($incourse_layout_focus == 1) {
  array_push($extraclasses, "ccn_context_dashboard ccn_context_focus");
  $bodyclasses = implode(" ",$extraclasses);
  $bodyattributes = $OUTPUT->body_attributes($bodyclasses);
  include($CFG->dirroot . '/theme/edumy/ccn/ccn_themehandler_context.php');
  echo $OUTPUT->render_from_template('theme_edumy/ccn_focus', $templatecontext);
} else {
  array_push($extraclasses, "ccn_context_frontend");
  $bodyclasses = implode(" ",$extraclasses);
  $bodyattributes = $OUTPUT->body_attributes($bodyclasses);
  include($CFG->dirroot . '/theme/edumy/ccn/ccn_themehandler_context.php');
  echo $OUTPUT->render_from_template('theme_boost/columns2', $templatecontext);
}
