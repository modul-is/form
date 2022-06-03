/**
 * @return {boolean}
 */
Nette.validators.CodeComponentFormValidator_greater = function(elem, args, val)
{
    return parseInt(val) > parseInt(args);
};

/**
 * @return {boolean}
 */
Nette.validators.CodeComponentFormValidator_less = function(elem, args, val)
{
    return parseInt(val) < parseInt(args);
};

/**
 * @return {boolean}
 */
Nette.validators.CodeComponentFormValidator_sameLength = function(elem, args, val)
{
    return args.length === val.length;
};
