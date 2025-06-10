class ProductoCRUD {
    constructor() {
        this.apiUrl = 'api/productos.php';
        this.isEditing = false;
        this.currentProductoId = null;
        
        this.initEventListeners();
        this.cargarProductos();
    }

    initEventListeners() {
        const form = document.getElementById('producto-form');
        const cancelBtn = document.getElementById('cancel-btn');
        
        form.addEventListener('submit', (e) => this.handleSubmit(e));
        cancelBtn.addEventListener('click', () => this.cancelEdit());
    }

    async cargarProductos() {
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            
            this.renderProductos(data.registros || []);
        } catch (error) {
            console.error('Error al cargar productos:', error);
            this.showMessage('Error al cargar productos', 'error');
        }
    }

    renderProductos(productos) {
        const tbody = document.getElementById('productos-tbody');
        
        if (productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="loading">No hay productos registrados</td></tr>';
            return;
        }

        tbody.innerHTML = productos.map(producto => `
            <tr>
                <td>${producto.id}</td>
                <td>${producto.nombre}</td>
                <td>$${parseFloat(producto.precio).toFixed(2)}</td>
                <td>
                    <button class="btn-edit" onclick="productoCRUD.editarProducto(${producto.id}, '${producto.nombre}', ${producto.precio})">
                        Editar
                    </button>
                    <button class="btn-delete" onclick="productoCRUD.eliminarProducto(${producto.id})">
                        Eliminar
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        const nombre = document.getElementById('nombre').value;
        const precio = document.getElementById('precio').value;
        
        const productoData = {
            nombre: nombre,
            precio: parseFloat(precio)
        };

        try {
            if (this.isEditing) {
                await this.actualizarProducto(productoData);
            } else {
                await this.crearProducto(productoData);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showMessage('Error al procesar la solicitud', 'error');
        }
    }

    async crearProducto(productoData) {
        const response = await fetch(this.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(productoData)
        });

        if (response.ok) {
            this.showMessage('Producto creado exitosamente', 'success');
            this.resetForm();
            this.cargarProductos();
        } else {
            throw new Error('Error al crear producto');
        }
    }

    async actualizarProducto(productoData) {
        productoData.id = this.currentProductoId;
        
        const response = await fetch(this.apiUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(productoData)
        });

        if (response.ok) {
            this.showMessage('Producto actualizado exitosamente', 'success');
            this.cancelEdit();
            this.cargarProductos();
        } else {
            throw new Error('Error al actualizar producto');
        }
    }

    async eliminarProducto(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar este producto?')) {
            return;
        }

        try {
            const response = await fetch(this.apiUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            });

            if (response.ok) {
                this.showMessage('Producto eliminado exitosamente', 'success');
                this.cargarProductos();
            } else {
                throw new Error('Error al eliminar producto');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showMessage('Error al eliminar producto', 'error');
        }
    }

    editarProducto(id, nombre, precio) {
        this.isEditing = true;
        this.currentProductoId = id;
        
        document.getElementById('producto-id').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('precio').value = precio;
        
        document.getElementById('form-title').textContent = 'Editar Producto';
        document.getElementById('submit-btn').textContent = 'Actualizar';
        document.getElementById('cancel-btn').style.display = 'inline-block';
    }

    cancelEdit() {
        this.isEditing = false;
        this.currentProductoId = null;
        this.resetForm();
    }

    resetForm() {
        document.getElementById('producto-form').reset();
        document.getElementById('producto-id').value = '';
        document.getElementById('form-title').textContent = 'Agregar Producto';
        document.getElementById('submit-btn').textContent = 'Agregar';
        document.getElementById('cancel-btn').style.display = 'none';
    }

    showMessage(message, type) {
        // Remover mensajes anteriores
        const existingMessage = document.querySelector('.message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        
        const container = document.querySelector('.container');
        container.insertBefore(messageDiv, container.firstChild);
        
        // Remover el mensaje después de 3 segundos
        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }
}

// Inicializar la aplicación cuando se carga la página
const productoCRUD = new ProductoCRUD();