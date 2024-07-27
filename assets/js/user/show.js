document.onload = sameWidthBtn()

function sameWidthBtn() {
    let deleteBtn   = document.getElementById('delete')
    let editBtn     = document.getElementById('edit')

    editBtn.style.width = deleteBtn.offsetWidth + 'px'
}