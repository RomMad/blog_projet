<div class="row">
    <div class="col-md-6">
        <form action="<?= $linkNbDisplayed ?>" method="post" class="form-inline">
            <label class="mr-2 col-form-label-sm" for="action">Action</label>
            <select name="nbDisplayed" id="action" class="custom-select mr-sm-2 form-control-sm" >
                <option value="edit">Modifier</option>
                <option value="erase">Supprimer</option>
            </select>
            <button type="submit" class="btn btn-info form-control-sm">Appliquer</button>
        </form>
    </div>

    <div class="col-md-6">

    </div>
</div>