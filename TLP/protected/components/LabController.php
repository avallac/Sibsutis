<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
abstract class LabController extends Controller
{
    public $labNum;
    public $formName;

    public function actionIndex()
    {
        $model = new $this->formName;
        $labModel = array();
        if (isset($_POST[$this->formName])) {
            $model->attributes = $_POST[$this->formName];
            if ($model->validate()) {
                $labModel = $this->runLab($model);
            }
        }
        $this->render('index', array('model'=>$model, 'labModel' => $labModel));
    }

    public function actionSave()
    {
        $model = new $this->formName;
        $saveModel = new SaveForm;
        if (isset($_POST['SaveForm'])) {
            $saveModel->attributes = $_POST['SaveForm'];
            $model->attributes = json_decode($_POST['SaveForm']['form'], 1);
            if ($saveModel->validate() && $model->validate()) {
                $case = new CaseRecord();
                $case->labNum = $this->labNum;
                $case->name = $saveModel->filename;
                $case->rule = $saveModel->form;
                $case->save();
            }
        }
        $this->render('index', array('model'=>$model));
    }

    public function actionLoad($id)
    {
        $case = CaseRecord::model()->find('id=:ID', array(':ID'=>$id));
        $model = new $this->formName;
        $model->attributes = json_decode($case->rule, 1);
        $this->render('index', array('model'=>$model));
    }

    public function actionDelete($id)
    {
        $case = CaseRecord::model()->find('id=:ID', array(':ID'=>$id));
        $case->delete();
        $this->redirect('index');
    }
}
