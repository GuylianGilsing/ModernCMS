class InfoPopups
{
    #infoPopupContainer = null;
    #runAnimClass = 'run-anim';

    constructor(querySelectorString)
    {
        if (typeof querySelectorString !== 'string')
        {
            return;
        }

        this.#infoPopupContainer = document.querySelector(querySelectorString);

        if (!(this.#infoPopupContainer instanceof Element))
        {
            return;
        }

        this.#infoPopupContainer.addEventListener('click', (e) => this.#EVENTInfoPopupContainerClick(e));
    }

    canDisplayOnePopup()
    {
        return this.#infoPopupContainer.children.length > 0;
    }

    displayOnePopup()
    {
        if (this.canDisplayOnePopup())
        {
            const popup = this.#infoPopupContainer.children[0];
            popup.classList.add(this.#runAnimClass);
        }
    }

    #EVENTInfoPopupContainerClick(e)
    {
        if (e.target.hasAttribute('data-popup-action'))
        {
            switch (e.target.getAttribute('data-popup-action'))
            {
                case 'close':
                    e.target.parentElement.remove();

                    if (this.canDisplayOnePopup())
                    {
                        this.displayOnePopup();
                    }
                    break;
            }
        }
    }
}
