<?php
class PluginProcedimientosSection extends CommonDBChild
{
   static public $itemtype = "PluginFormcreatorForm";
   static public $items_id = "plugin_formcreator_forms_id";

   /**
    * Check if current user have the right to create and modify requests
    *
    * @return boolean True if he can create and modify requests
    */
   public static function canCreate() {
      return true;
   }

   /**
    * Check if current user have the right to read requests
    *
    * @return boolean True if he can read requests
    */
   public static function canView() {
      return true;
   }

   /**
    * Returns the type name with consideration of plural
    *
    * @param number $nb Number of item(s)
    * @return string Itemtype name
    */
   public static function getTypeName($nb = 0) {
      return _n('Section', 'Sections', $nb, 'formcreator');
   }

   /**
    * Prepare input datas for adding the section
    * Check fields values and get the order for the new section
    *
    * @param $input datas used to add the item
    *
    * @return the modified $input array
   **/
   public function prepareInputForAdd($input) {
      global $DB;

      // Decode (if already encoded) and encode strings to avoid problems with quotes
      foreach ($input as $key => $value) {
         $input[$key] = plugin_formcreator_encode($value);
      }

      // Control fields values :
      // - name is required
      if (!isset($input['name']) ||
         (isset($input['name']) && empty($input['name'])) ) {
         Session::addMessageAfterRedirect(__('The title is required', 'formcreator'), false, ERROR);
         return array();
      }
      $input['name'] = addslashes($input['name']);

      // generate a uniq id
      if (!isset($input['uuid'])
          || empty($input['uuid'])) {
         $input['uuid'] = plugin_formcreator_getUuid();
      }

      // Get next order
      $table  = self::getTable();
      $query  = "SELECT MAX(`order`) AS `order`
                 FROM `$table`
                 WHERE `plugin_formcreator_forms_id` = {$input['plugin_formcreator_forms_id']}";
      $result = $DB->query($query);

	// [INICIO] [CRI] [JMZ18G] fetch_array deprecated function	
	// $line   = $DB->fetch_array($result);
      $line   = $DB->fetchAssoc($result);      
   // [FINAL] [CRI] [JMZ18G] fetch_array deprecated function	      

      $input['order'] = $line['order'] + 1;

      return $input;
   }

   /**
    * Prepare input datas for updating the form
    *
    * @param $input datas used to add the item
    *
    * @return the modified $input array
   **/
   public function prepareInputForUpdate($input) {
      // Decode (if already encoded) and encode strings to avoid problems with quotes
      foreach ($input as $key => $value) {
         $input[$key] = plugin_formcreator_encode($value);
      }

      // Control fields values :
      // - name is required
      if (isset($input['name'])
            && empty($input['name'])) {
         Session::addMessageAfterRedirect(__('The title is required', 'formcreator'), false, ERROR);
         return array();
      }

      // generate a uniq id
      if (!isset($input['uuid'])
            || empty($input['uuid'])) {
         $input['uuid'] = plugin_formcreator_getUuid();
      }

      return $input;
   }


   /**
    * Actions done after the PURGE of the item in the database
    * Reorder other sections
    *
    * @return nothing
   **/
   public function post_purgeItem() {
      global $DB;

      $table = self::getTable();
      $query = "UPDATE `$table` SET
                  `order` = `order` - 1
                WHERE `order` > {$this->fields['order']}
                AND plugin_formcreator_forms_id = {$this->fields['plugin_formcreator_forms_id']}";
      $DB->query($query);

      $question = new PluginFormcreatorQuestion();
      $question->deleteByCriteria(array('plugin_formcreator_sections_id' => $this->getID()), 1);
   }

   /**
    * Duplicate a section
    *
    * @return boolean
    */
   public function duplicate() {
      $oldSectionId        = $this->getID();
      $newSection          = new static();
      $section_question    = new PluginFormcreatorQuestion();
      $question_condition  = new PluginFormcreatorQuestion_Condition();

      $tab_questions       = array();

      $row = $this->fields;
      unset($row['id'],
            $row['uuid']);
      if (!$newSection->add($row)) {
         return false;
      }

      // Form questions

$params = [
"plugin_formcreator_sections_id" => $oldSectionId,
];		  
	  
      $rows = $section_question->find($params,['order ASC']);
	  
      foreach ($rows as $questions_id => $row) {
         unset($row['id'],
               $row['uuid']);
         $row['plugin_formcreator_sections_id'] = $newSection->getID();
         if (!$new_questions_id = $section_question->add($row)) {
            return false;
         }

         $tab_questions[$questions_id] = $new_questions_id;
      }

      // Form questions conditions
      $questionIds = implode("', '", array_keys($tab_questions));
$params = [
"plugin_formcreator_questions_id" => [$questionIds],
];	
	  
      $rows = $question_condition->find($params);
      foreach ($rows as $conditions_id => $row) {
         unset($row['id'],
               $row['uuid']);
         if (isset($tab_questions[$row['show_field']])) {
            // update show_field if id in show_field belongs to the section being duplicated
            $row['show_field'] = $tab_questions[$row['show_field']];
         }
         $row['plugin_formcreator_questions_id'] = $tab_questions[$row['plugin_formcreator_questions_id']];
         if (!$new_conditions_id = $question_condition->add($row)) {
            return false;
         }
      }

      return true;
   }


   public function moveUp() {
      $order         = $this->fields['order'];
      $formId        = $this->fields['plugin_formcreator_forms_id'];
      $otherItem = new static();
	  
      //[INICIO] CRI - JMZ18G getFromDBByQuery es una función obsoleta.
        $otherItem->getFromDBByRequest([
         'WHERE' => [
            'AND' => [
               "plugin_formcreator_forms_id" => $formId,
			   'order'                       => ['<', $order]			   
            ]
         ],
         'ORDER' => ['order DESC'],
         'LIMIT' => 1
      ]);
	 //[FINAL] CRI - JMZ18G getFromDBByQuery es una función obsoleta.		  
	  
     /* $otherItem->getFromDBByQuery("WHERE `plugin_formcreator_forms_id` = '$formId'
            AND `order` < '$order'
            ORDER BY `order` DESC LIMIT 1");*/
			
      if (!$otherItem->isNewItem()) {
         $this->update(array(
               'id'     => $this->getID(),
               'order'  => $otherItem->getField('order'),
         ));
         $otherItem->update(array(
               'id'     => $otherItem->getID(),
               'order'  => $order,
         ));
      }
   }

   public function moveDown() {
      $order         = $this->fields['order'];
      $formId     = $this->fields['plugin_formcreator_forms_id'];
      $otherItem = new static();
	  
      //[INICIO] CRI - JMZ18G getFromDBByQuery es una función obsoleta.
        $otherItem->getFromDBByRequest([
         'WHERE' => [
            'AND' => [
               "plugin_formcreator_forms_id" => $formId,
			   'order'                       => ['>', $order]			   
            ]
         ],
         'ORDER' => ['order ASC'],
         'LIMIT' => 1
      ]);
	 //[FINAL] CRI - JMZ18G getFromDBByQuery es una función obsoleta.	  
	  
    /*  $otherItem->getFromDBByQuery("WHERE `plugin_formcreator_forms_id` = '$formId'
            AND `order` > '$order'
            ORDER BY `order` ASC LIMIT 1");*/
			
      if (!$otherItem->isNewItem()) {
         $this->update(array(
               'id'     => $this->getID(),
               'order'  => $otherItem->getField('order'),
         ));
         $otherItem->update(array(
               'id'     => $otherItem->getID(),
               'order'  => $order,
         ));
      }
   }

   /**
    * Import a form's section into the db
    * @see PluginFormcreatorForm::importJson
    *
    * @param  integer $forms_id  id of the parent form
    * @param  array   $section the section data (match the section table)
    * @return integer the section's id
    */
   public static function import($forms_id = 0, $section = array()) {
      $item = new self;

      $section['plugin_formcreator_forms_id'] = $forms_id;
      $section['_skip_checks']                = true;

      if ($sections_id = plugin_formcreator_getFromDBByField($item, 'uuid', $section['uuid'])) {
         // add id key
         $section['id'] = $sections_id;

         // update section
         $item->update($section);
      } else {
         //create section
         $sections_id = $item->add($section);
      }

      if ($sections_id
          && isset($section['_questions'])) {
         // sort questions by order
         usort($section['_questions'], function ($a, $b) {
            if ($a['order'] == $b['order']) {
               return 0;
            }
               return ($a['order'] < $b['order']) ? -1 : 1;
         }
         );

         foreach ($section['_questions'] as $question) {
            PluginFormcreatorQuestion::import($sections_id, $question);
         }
      }

      return $sections_id;
   }

   /**
    * Export in an array all the data of the current instanciated section
    * @param boolean $remove_uuid remove the uuid key
    *
    * @return array the array with all data (with sub tables)
    */
   public function export($remove_uuid = false) {
      if (!$this->getID()) {
         return false;
      }

      $form_question = new PluginFormcreatorQuestion;
      $section       = $this->fields;

      // remove key and fk
      unset($section['id'],
            $section['plugin_formcreator_forms_id']);

      // get questions
      $section['_questions'] = [];
	  
	  $params = [
"plugin_formcreator_sections_id" => $this->getID(),
];		  	      
	  
      $all_questions = $form_question->find($params);
      foreach ($all_questions as $questions_id => $question) {
         if ($form_question->getFromDB($questions_id)) {
            $section['_questions'][] = $form_question->export($remove_uuid);
         }
      }

      if ($remove_uuid) {
         $section['uuid'] = '';
      }

      return $section;
   }

   /**
    * get all sections in a form
    */
   public function getSectionsFromForm($formId) {
      $sections = array();
	  
	  $params = [
"plugin_formcreator_forms_id" => $formId,
];		  	         
	  
      $rows = $this->find($params, ['order ASC']);
      foreach ($rows as $sectionId => $row) {
         $section = new self();
         $section->getFromDB($sectionId);
         $sections[] = $section;
      }

      return $sections;
   }

   public function showSubForm($ID) {
      global $CFG_GLPI;

      if ($ID == 0) {
         $name          = '';
         $uuid          = '';
      } else {
         $name          = $this->fields['name'];
         $uuid          = $this->fields['uuid'];
      }
      echo '<form name="form_section" method="post" action="'.static::getFormURL().'">';
      echo '<table class="tab_cadre_fixe">';
      echo '<tr>';
      echo '<th colspan="2">';
      if ($ID == 0) {
         echo  __('Add a section', 'formcreator');
      } else {
         echo  __('Edit a section', 'formcreator');
      }
      echo '</th>';
      echo '</tr>';

      echo '<tr class="line0">';
      echo '<td width="20%">'.__('Title').' <span style="color:red;">*</span></td>';
      echo '<td width="70%"><input type="text" name="name" style="width:100%;" value="'.$name.'" class="required"></td>';
      echo '</tr>';

      echo '<tr class="line1">';
      echo '<td colspan="2" class="center">';
      echo '<input type="hidden" name="id" value="'.$ID.'" />';
      echo '<input type="hidden" name="uuid" value="'.$uuid.'" />';
      echo '<input type="hidden" name="plugin_formcreator_forms_id" value="'.intval($_REQUEST['form_id']).'" />';
      if ($ID == 0) {
         echo '<input type="hidden" name="add" value="1" />';
         echo '<input type="submit" name="add" class="submit_button" value="'.__('Add').'" />';
      } else {
         echo '<input type="hidden" name="update" value="1" />';
         echo '<input type="submit" name="update" class="submit_button" value="'.__('Edit').'" />';
      }
      echo '</td>';
      echo '</tr>';

      echo '</table>';
      Html::closeForm();
   }
}
