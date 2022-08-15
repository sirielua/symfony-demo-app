
const $ = require('jquery');

const addDeleteButton = function(item)
{
    const list = item.parent('.form-collection-widget');
    const deleteButton = $(list.attr('data-widget-delete-button'));
    
    deleteButton.on('click', (e) => {
        e.preventDefault();
        item.remove();
    });
    
    deleteButton.appendTo(item);
};

$('.form-collection-widget > .item').each(function() {
    addDeleteButton($(this));
});

$('.add-another-collection-widget').click(function (e) {
    const list = $($(this).attr('data-list-selector'));
    // Try to find the counter of the list or use the length of the list
    let counter = list.data('widget-counter') || list.children().length;

    // grab the prototype template
    let newWidget = list.attr('data-prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, counter);
    // Increase the counter
    counter++;
    // And store it, the length cannot be used if deleting widgets is allowed
    list.data('widget-counter', counter);

    // create a new list element and add it to the list
    const newElem = $(list.attr('data-widget-tags')).html(newWidget);
    
    newElem.appendTo(list);
    addDeleteButton(newElem);
});