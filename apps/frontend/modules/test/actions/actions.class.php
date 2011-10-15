<?php

/**
 * test actions.
 *
 * @package    aop_test
 * @subpackage test
 * @author     Your name here
 */
class testActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->TestTables = TestTableQuery::create()->find();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TestTableForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new TestTableForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $TestTable = TestTableQuery::create()->findPk($request->getParameter('id'));
    $this->forward404Unless($TestTable, sprintf('Object TestTable does not exist (%s).', $request->getParameter('id')));
    $this->form = new TestTableForm($TestTable);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $TestTable = TestTableQuery::create()->findPk($request->getParameter('id'));
    $this->forward404Unless($TestTable, sprintf('Object TestTable does not exist (%s).', $request->getParameter('id')));
    $this->form = new TestTableForm($TestTable);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $TestTable = TestTableQuery::create()->findPk($request->getParameter('id'));
    $this->forward404Unless($TestTable, sprintf('Object TestTable does not exist (%s).', $request->getParameter('id')));
    $TestTable->delete();

    $this->redirect('test/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $TestTable = $form->save();

      $this->redirect('test/edit?id='.$TestTable->getId());
    }
  }
}
