<div class="modal fade" id="modalAgregarArbol" tabindex="-1" aria-labelledby="modalAgregarArbolLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalAgregarArbolLabel">Nueva carga de Árboles</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarArbol" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date" class="form-label fw-bold">Fecha</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label fw-bold">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                    </div>
                    <div class="mb-3">
                        <label for="especie" class="form-label fw-bold">Especie</label>
                        <input type="text" class="form-control" id="especie" name="especie">
                    </div>
                    <div class="mb-3">
                        <label for="publicoprivado" class="form-label fw-bold">Público/Privado</label>
                        <select class="form-select" id="publicoprivado" name="publicoprivado" required>
                            <option value="Público">Público</option>
                            <option value="Privado">Privado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quienLoPlanto" class="form-label fw-bold">Quién lo plantó</label>
                        <input type="text" class="form-control" id="quienLoPlanto" name="quienLoPlanto">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="imagen" class="form-label fw-bold">Imagen</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                    </div>
                    <input type="hidden" id="municipio" name="municipio" value="<?php echo $_SESSION['id_municipio']; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>