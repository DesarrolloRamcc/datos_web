<div class="modal fade" id="modalEditarArbol" tabindex="-1" aria-labelledby="modalEditarArbolLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalEditarArbolLabel">Editar carga de Árboles</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarArbol" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_date" class="form-label fw-bold">Fecha</label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cantidad" class="form-label fw-bold">Cantidad</label>
                        <input type="number" class="form-control" id="edit_cantidad" name="cantidad" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_especie" class="form-label fw-bold">Especie</label>
                        <input type="text" class="form-control" id="edit_especie" name="especie">
                    </div>
                    <div class="mb-3">
                        <label for="edit_publicoprivado" class="form-label fw-bold">Público/Privado</label>
                        <select class="form-select" id="edit_publicoprivado" name="publicoprivado" required>
                            <option value="Público">Público</option>
                            <option value="Privado">Privado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_quienLoPlanto" class="form-label fw-bold">Quién lo plantó</label>
                        <input type="text" class="form-control" id="edit_quienLoPlanto" name="quienLoPlanto" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_imagen" class="form-label fw-bold">Imagen (formato jpg, jpeg, png)</label>
                        <input type="file" class="form-control" id="edit_imagen" name="imagen" accept="image/*">
                        <div id="imagenActual" class="mt-2"></div>
                    </div>
                </div>
                <!-- Reemplazar la línea comentada con -->
                <input type="hidden" id="edit_municipio" name="municipio" value="<?php echo $id_municipio; ?>">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>