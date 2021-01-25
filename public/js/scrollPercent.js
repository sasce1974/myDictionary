/** Shows percentage of scrolled page in the title */
// Thanks to Chris Coyier's article:
// https://css-tricks.com/how-i-put-the-scroll-percentage-in-the-browser-title-bar/
const originalTitle = document.title;
window.addEventListener("scroll", ()=>{
    let scrollTop = window.scrollY;
    let docHeight = document.body.offsetHeight;
    let winHeight = window.innerHeight;
    let scrollPercent = Math.round((scrollTop / (docHeight - winHeight)) * 100);
    if(scrollTop === 0){
        document.title = originalTitle;
    }else{
        document.title = `(${scrollPercent}%) ${originalTitle}`;
    }

});