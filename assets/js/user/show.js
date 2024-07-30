import 'bootstrap';
document.onload = sameWidthBtn()

function sameWidthBtn() {
    let deleteBtn   = document.getElementById('delete')
    let editBtn     = document.getElementById('edit')
    let passwordBtn = document.getElementById('password')
    let greatest    = 0

    // +1 is here because some times it's 123.25px and offset width equals 123 so element didn't display well
    if (deleteBtn.offsetWidth > greatest)
        greatest = deleteBtn.offsetWidth + 1
    if (editBtn.offsetWidth > greatest)
        greatest = deleteBtn.offsetWidth + 1 
    if (passwordBtn.offsetWidth > greatest)
        greatest = passwordBtn.offsetWidth + 1

    deleteBtn.style.width   = greatest + 'px'
    editBtn.style.width     = greatest + 'px'
    passwordBtn.style.width = greatest + 'px'
}