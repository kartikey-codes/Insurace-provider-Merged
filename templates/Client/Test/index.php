<!-- File: templates/Mirth/index.ctp -->

<!DOCTYPE html>
<html>
<head>
   
</head>
<body>
    <!-- File: templates/Client/Test/index.ctp -->

    <h1>Enter Patient's Name to Search</h1>

    <?= $this->Flash->render() ?>

    <?= $this->Form->create(null, ['url' => ['action' => 'index']]) ?>
        <div class="form-group">
            <?= $this->Form->control('patient_name', ['label' => 'Patient Name']) ?>
        </div>
        <div class="form-group">
            <?= $this->Form->submit('Search', ['class' => 'btn btn-primary']) ?>
        </div>
    <?= $this->Form->end() ?>

</body>
</html>
