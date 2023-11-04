$(document).ready(function () {
    window.prestashop.component.initComponents(
        [
            'TranslatableField',
            'TinyMCEEditor',
            'TranslatableInput',
        ],
    );

    const choiceTree = new window.prestashop.component.ChoiceTree('.js-choice-tree-container');

    choiceTree.enableAutoCheckChildren();
});

