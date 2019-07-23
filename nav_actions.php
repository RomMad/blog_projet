<div class="row">
    <div class="col-md-6">
        <form action="<?= $linkNbDisplayed ?>" method="post" class="form-inline">
            <label class="mr-2 col-form-label-sm" for="action">Action</label>
            <select name="action" id="action" class="custom-select mr-sm-2 form-control-sm" >
                <option value="edit">Modifier</option>
                <option value="erase">Supprimer</option>
            </select>
            <input type="submit" id="action_admin" class="btn btn-info form-control-sm pt-1" value="Appliquer">
        </form>
    </div>

    <div class="col-md-6">
    <a href="
    <?php 
    $linkNbDisplayed 
    
    ?>
    
    
    " id="action_admin" class="btn btn-info text-white form-control-sm pt-1" value="Appliquer">Appliquer</a>


    </div>
</div>