import {Controller} from "@hotwired/stimulus"

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String,
    };

    connect() {
        this.index = this.element.childElementCount;
        const btn = document.createElement('button');
        btn.setAttribute('class', 'btn btn-secondary btn-sm');
        btn.innerText = this.addLabelValue || 'Ajouter un élément';
        btn.setAttribute('type', 'button');
        btn.addEventListener('click', this.addElementButton);
        this.element.childNodes.forEach(this.deleteElementButton);
        this.element.append(btn);
    }

    addElementButton = (e) => {
        e.preventDefault();
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild;
        this.deleteElementButton(element);
        this.index++;
        e.currentTarget.insertAdjacentElement('beforebegin', element);
    }

    /**
     * @param {HTMLElement} item
     */
    deleteElementButton = (item) => {
        const btn = document.createElement('button');
        btn.setAttribute('class', 'btn btn-danger btn-sm');
        btn.innerText = this.deleteLabelValue || 'Supprimer';
        btn.setAttribute('type', 'button');
        item.append(btn);
        btn.addEventListener('click', () => {
            item.remove();
        });
    }
}
