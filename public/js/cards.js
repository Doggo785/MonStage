/*=============== CARD POPUP JS ===============*/
const modal = document.querySelectorAll('.modal'),
      cardBtn = document.querySelectorAll('.card__product'),
      modalClose = document.querySelectorAll('.modal__close'),
      modalCard = document.querySelectorAll('.modal__card')
      card = document.querySelectorAll('.card__box')

let activeModal = (modalClick) =>{
   modal[modalClick].classList.add('active-modal')
}

/* Show modal */
cardBtn.forEach((cardBtn, i) =>{
   cardBtn.addEventListener('click', () =>{
      activeModal(i)
   })
})

/* Hide modal */
modalClose.forEach((modalClose) =>{
   modalClose.addEventListener('click', () => {
       modal.forEach((modalRemove) => {
           modalRemove.classList.remove('active-modal')
       })
   })
})

/* Hide modal on background click */
modal.forEach((modal) =>{
   modal.addEventListener('click', () =>{
      modal.classList.remove('active-modal')
   })
})

/* Don't hide modal on card click (by event propagation) */
modalCard.forEach((modalCard) =>{
   modalCard.addEventListener('click', (e) =>{
      e.stopPropagation()
   })
})

/*=============== CARD HOVER EFFECT ===============*/
card.forEach(el =>{
    el.addEventListener("mousemove",e=>{
        let elRect = el.getBoundingClientRect()

        let x = e.clientX - elRect.x
        let y = e.clientY - elRect.y

        let midCardWidth = elRect.width/2
        let midCardHeight = elRect.height/2

        let angleY = -(x-midCardWidth)/15
        let angleX = (y-midCardWidth)/15
        
        el.children[0].style.transform = `rotateX(${angleX}deg) rotateY(${angleY}deg) scale(1.05)`
    })

    el.addEventListener("mouseleave",()=>{
        el.children[0].style.transform = "rotateX(0) rotateY(0) scale(1)"
    })

})