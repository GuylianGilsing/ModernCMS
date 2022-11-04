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

            // Popup timeout
            const timeout = this.#getTimeoutFromPopup(popup);

            this.#applyTimeoutToPopup(popup, timeout);
            this.#applyTimeoutAnimationToPopupProgressBar(popup);
        }
    }

    #getTimeoutFromPopup(popup)
    {
        if (!(popup instanceof Element))
        {
            return 0;
        }

        let timeout = 0;

        if (popup.hasAttribute('data-timeout'))
        {
            const timeoutInMiliseconds = Number.parseInt(popup.getAttribute('data-timeout'))

            if (!isNaN(timeoutInMiliseconds))
            {
                timeout = timeoutInMiliseconds;
            }
        }

        return timeout;
    }

    #applyTimeoutToPopup(popup, timeout)
    {
        if (!(popup instanceof Element))
        {
            return;
        }

        if (!Number.isInteger(timeout))
        {
            return;
        }

        if (timeout > 0)
        {
            const deletePopupCallback = () =>
            {
                popup.remove();

                if (this.canDisplayOnePopup())
                {
                    this.displayOnePopup();
                }
            };

            setTimeout(deletePopupCallback, timeout);
        }
    }

    #applyTimeoutAnimationToPopupProgressBar(popup)
    {
        if (!(popup instanceof Element))
        {
            return;
        }

        const progressBar = popup.querySelector('.info-popup-progress-bar-animation');

        if (progressBar === null)
        {
            return;
        }

        progressBar.classList.add(this.#runAnimClass);
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
