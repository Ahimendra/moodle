<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

/**
 * The form for handling featured courses.
 */
class cocoon_more_courses_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $availablecourses  = $this->_customdata['availablecourses'];
        $cocoon_more_courses  = $this->_customdata['cocoon_more_courses'];
        $availablecourseslist = array();
        foreach ($availablecourses as $c) {
            $availablecourseslist[$c->id] = $c->shortname . ' : ' . $c->fullname;
        }

        // Forms to edit existing featured courses.
        foreach ($cocoon_more_courses as $c) {
            $mform->addElement('header', 'featured',
                               get_string('featuredcourse', 'block_cocoon_more_courses', $c->shortname . ' - '. $c->fullname));

            $mform->addElement('hidden', 'featured['.$c->id.'][id]', null);
            $mform->setType('featured['.$c->id.'][id]', PARAM_INT);
            $mform->setConstant('featured['.$c->id.'][id]', $c->id);

            $mform->addElement('text', 'featured['.$c->id.'][sortorder]', get_string('sortorder', 'block_cocoon_more_courses'));
            $mform->addRule('featured['.$c->id.'][sortorder]',
                            get_string('missingsortorder', 'block_cocoon_more_courses'), 'required', null, 'client');
            $mform->setType('featured['.$c->id.'][sortorder]', PARAM_INT);
            $mform->setDefault('featured['.$c->id.'][sortorder]', $c->sortorder);

            $mform->addElement('static', 'link',
                               get_string('deletelink', 'block_cocoon_more_courses',
                                          $CFG->wwwroot.'/blocks/cocoon_more_courses/delete_cocoon_more_courses_course.php?courseid='.$c->id));

        }

        // Add a new featured course.
        $mform->addElement('header', 'add', get_string('addfeaturedcourse', 'block_cocoon_more_courses'));

        $mform->addElement('checkbox', 'doadd', get_string('doadd', 'block_cocoon_more_courses'));

        $mform->addElement('select', 'newfeatured[courseid]',
                           get_string('courseid', 'block_cocoon_more_courses'), $availablecourseslist);
        $mform->addRule('newfeatured[courseid]', get_string('missingcourseid', 'block_cocoon_more_courses'),
                        'required', null, 'client');
        $mform->disabledIf('newfeatured[courseid]', 'doadd', 'notchecked');

        $mform->addElement('text', 'newfeatured[sortorder]', get_string('sortorder', 'block_cocoon_more_courses'));
        $mform->addRule('newfeatured[sortorder]', get_string('missingsortorder', 'block_cocoon_more_courses'),
                        'required', null, 'client');
        $mform->setType('newfeatured[sortorder]', PARAM_INT);
        $mform->disabledIf('newfeatured[sortorder]', 'doadd', 'notchecked');

        $mform->addElement('submit', 'save', get_string('savechanges'));

        $mform->closeHeaderBefore('save');
    }
}
