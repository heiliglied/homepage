export default class MultiModalControl {
    zIndex = 10000;
    draggable = false;
    setElement;
    bodyFix = true;
    dragArea = false;
    mouseClick = false;
    posX = 0;
    posY = 0;
	
	constructor(options) {
        if(options != undefined) {
            if(options.bodyFix != undefined && typeof(options.bodyFix) === 'boolean') {
                this.bodyFix = options.bodyFix;
            }
    
            if(options.index != undefined && typeof(options.index) === 'number') {
                this.zIndex = options.index;
            }
        }
    }
	
	setModal(depth, element, vail, autoClose, draggable, dragArea) {
        let newElement = element.cloneNode(true);
        
        //id 항목들이 있을 시 겹치지 않도록 모든 ID에 -clone-depth를 추가한다.
        this.setElement = newElement;
        let elementIds = this.setElement.querySelectorAll("[id]");
        elementIds.forEach((e) => {
            e.id += '-clone-' + depth;
        });
        
        this.draggable = draggable;
        if(dragArea != undefined && typeof(dragArea) === 'boolean') {
            this.dragArea = dragArea;
        }
        
        let mouseClick = this.mouseClick;
        let posX = this.posX;
        let posY = this.posY;

        if(vail == true) {
            let vailId = 'multi-vail-' + depth;
            let vailElement = document.createElement('div');
            vailElement.setAttribute('id', vailId);
            vailElement.setAttribute('style', 'position: absolute; top:0; left:0; bottom: 0; width:100%; height:100%; background:#000; z-index:' + (this.zIndex + depth) + '; opacity:0.3;');
            if(autoClose == true) {
                vailElement.addEventListener('click', () => { this.closeModal(depth); });
            }
            document.body.insertAdjacentElement('beforeend', vailElement);
        }

        let modalElement = document.createElement('div');
        let modalId = 'multi-modal-' + depth;
        let modalWidth = this.setElement.style.width;
        let modalHeight = this.setElement.style.height;
        this.setElement.style.display = '';
        this.setElement.setAttribute('id', '');

        modalElement.setAttribute('id', modalId);
        modalElement.setAttribute('style', 'position: fixed; width: ' + modalWidth + '; height:' + modalHeight + '; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%); background:#FFF; z-index:' + (this.zIndex + depth + 1) + ';');

        if(this.draggable == true) {
            if(this.dragArea == true) {
                let dragElement = document.createElement('div');
                dragElement.style.width = modalWidth;
                dragElement.style.height = '20px';
                dragElement.style.backgroundColor = "#abcdef";
                modalHeight = String(Number(modalHeight.replace('px', '')) + 20) + 'px';

                modalElement.insertAdjacentElement('beforeend', dragElement);
                
                dragElement.addEventListener('mousedown', function(event){
                    mouseClick = true;
                    posX = event.clientX;
                    posY = event.clientY;
                });

                dragElement.addEventListener('mousemove', function(event){
                    if(mouseClick == true) {
                        let parentElement = this.parentElement; 

                        var now_posX = posX - event.clientX;
                        var now_posY = posY - event.clientY;

                        posX = event.clientX;
                        posY = event.clientY;

                        parentElement.style.left = (parentElement.offsetLeft - now_posX) + "px";
                        parentElement.style.top = (parentElement.offsetTop - now_posY) + "px";
                    }
                });

                dragElement.addEventListener('mouseup', function(event){
                    mouseClick = false;
                });

                document.addEventListener('mouseup', function(event){
                    mouseClick = false;
                });
            } else {
                modalElement.addEventListener('mousedown', function(event){
                    mouseClick = true;
                    posX = event.clientX;
                    posY = event.clientY;
                });

                modalElement.addEventListener('mousemove', function(event){
                    if(mouseClick == true) {
                        var now_posX = posX - event.clientX;
                        var now_posY = posY - event.clientY;

                        posX = event.clientX;
                        posY = event.clientY;

                        this.style.left = (this.offsetLeft - now_posX) + "px";
                        this.style.top = (this.offsetTop - now_posY) + "px";
                    }
                });

                modalElement.addEventListener('mouseup', function(event){
                    mouseClick = false;
                });

                document.addEventListener('mouseup', function(event){
                    mouseClick = false;
                });
            }
        }

        if(this.bodyFix == true) {
            document.body.style.overflow = 'hidden';
        }
        
        modalElement.insertAdjacentElement('beforeend', this.setElement);
        document.body.insertAdjacentElement('beforeend', modalElement);
        this.zIndex++;
    }
	
	closeModal(depth) {
        let modalId = 'multi-modal-' + depth;
        let vailId =  'multi-vail-' + depth;

        if(this.bodyFix == true) {
            document.body.style.overflow = '';
        }

        document.getElementById(modalId)?.remove();
        document.getElementById(vailId)?.remove();
    }

    selfCloseModal(element) {
        let modalIdValue = 'multi-modal-';
        let vailIdValue = 'multi-vail-';
        let htmlNode = element;
        while(true) {
            htmlNode = htmlNode.parentNode;
            if(htmlNode.tagName == 'body') {
                break;
            } else {
                if(htmlNode.id.indexOf(modalIdValue) != -1) {
                    let nodeId = htmlNode.id.split('-');
                    modalIdValue = modalIdValue + nodeId[2];
                    vailIdValue = vailIdValue + nodeId[2];
                    break;
                }
            }
        }

        document.getElementById(modalIdValue)?.remove();
        document.getElementById(vailIdValue)?.remove();
    }

    setModalReference(depth, vail, autoClose, draggable, contents) {
        this.draggable = draggable;
        let vailId = 'multi-vail-' + depth;

        if(vail == true) {
            
            let vailElement = document.createElement('div');
            vailElement.setAttribute('id', vailId);
            vailElement.setAttribute('style', 'position: absolute; top:0; left:0; bottom: 0; width:100%; height:100%; background:#000; z-index:' + (this.zIndex + depth) + '; opacity:0.3;');
            if(autoClose == true) {
                vailElement.addEventListener('click', () => { this.closeModal(depth); });
            }
            document.body.insertAdjacentElement('beforeend', vailElement);
        }

        let modalElement = document.createElement('div');
        let modalId = 'multi-modal-' + depth;
        let modalWidth = '480px';
        let modalHeight = 'auto';
        
        modalElement.setAttribute('id', modalId);
        modalElement.setAttribute('style', 'position: fixed; width: ' + modalWidth + '; height:' + modalHeight + '; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%); background:#FFF; z-index:' + (this.zIndex + depth + 1) + ';');

        let mouseClick = this.mouseClick;
        let posX = this.posX;
        let posY = this.posY;

        if(this.draggable == true) {
            let dragElement = document.createElement('div');
            dragElement.style.width = modalWidth;
            dragElement.style.height = '20px';
            dragElement.style.backgroundColor = "#abcdef";
            modalHeight = String(Number(modalHeight.replace('px', '')) + 20) + 'px';

            modalElement.insertAdjacentElement('beforeend', dragElement);
            
            dragElement.addEventListener('mousedown', function(event){
                mouseClick = true;
                posX = event.clientX;
                posY = event.clientY;
            });

            dragElement.addEventListener('mousemove', function(event){
                if(mouseClick == true) {
                    let parentElement = this.parentElement; 

                    var now_posX = posX - event.clientX;
                    var now_posY = posY - event.clientY;

                    posX = event.clientX;
                    posY = event.clientY;

                    parentElement.style.left = (parentElement.offsetLeft - now_posX) + "px";
                    parentElement.style.top = (parentElement.offsetTop - now_posY) + "px";
                }
            });

            dragElement.addEventListener('mouseup', function(event){
                mouseClick = false;
            });

            document.addEventListener('mouseup', function(event){
                mouseClick = false;
            });
        }

        if(this.bodyFix == true) {
            document.body.style.overflow = 'hidden';
        }

        let contentElemnt = document.createElement('div');
        contentElemnt.setAttribute('style', 'width: calc(100% - 10px); padding: 5px; background-color: white;');
        contentElemnt.innerText = contents;

        let elementIds = this.setElement.querySelectorAll("[id]");
        elementIds.forEach((e) => {
            e.id += '-clone-' + depth;
        });

        let footerElement = document.createElement('div');
        footerElement.setAttribute('style', 'width: calc(100% - 10px); border-top: 2px #000000 solid; padding: 5px; background-color: white; text-align: right');

        let closeButton = document.createElement('button');
        closeButton.setAttribute('id', 'multi-button-' + depth);
        closeButton.setAttribute('type', 'button');
        closeButton.innerText = '닫기';
        footerElement.insertAdjacentElement('beforeend', closeButton);

        closeButton.addEventListener('click', function(event){
            document.getElementById(modalId)?.remove();
            document.getElementById(vailId)?.remove();
        });

        modalElement.insertAdjacentElement('beforeend', contentElemnt);
        modalElement.insertAdjacentElement('beforeend', footerElement);

        document.body.insertAdjacentElement('beforeend', modalElement);
        this.zIndex++;
    }
}