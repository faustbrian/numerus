<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Math Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default mathematical computation engine that
    | will be used by Numerus throughout your application. The driver you
    | specify here will determine the precision and performance profile
    | of all mathematical operations performed by the package.
    |
    | Supported: "auto", "native", "bcmath", "gmp"
    |
    */

    'driver' => \Cline\Numerus\MathDriver::tryFrom(env('NUMERUS_DRIVER', 'native')) ?? \Cline\Numerus\MathDriver::Native,

    /*
    |--------------------------------------------------------------------------
    | Math Drivers
    |--------------------------------------------------------------------------
    |
    | Below are all of the math drivers available for your application. Each
    | driver offers different trade-offs between precision, performance, and
    | dependencies. You may configure which driver to use based on your
    | application's specific requirements for mathematical accuracy.
    |
    | Auto Driver
    | -----------
    | Automatically selects the best available adapter in this order:
    | BCMath > GMP > Native. This driver provides the most flexibility
    | as it requires no specific PHP extensions and will gracefully
    | fall back to native arithmetic if no extensions are available.
    |
    | Native Driver
    | -------------
    | Uses PHP's native floating-point arithmetic for all calculations.
    | This driver is the fastest option and is suitable for general-
    | purpose mathematics where exact decimal precision is not critical.
    | However, it is limited by IEEE 754 floating-point precision.
    |
    | BCMath Driver
    | -------------
    | Provides arbitrary precision decimal arithmetic using the BCMath
    | extension. This driver is ideal for financial calculations and
    | any operations requiring exact decimal representation. Requires
    | the BCMath PHP extension to be installed and enabled.
    |
    | GMP Driver
    | ----------
    | Offers arbitrary precision integer arithmetic with fixed-point
    | decimal support using the GMP extension. This driver excels at
    | cryptographic operations and very large integer calculations.
    | Requires the GMP PHP extension to be installed and enabled.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Decimal Scale
    |--------------------------------------------------------------------------
    |
    | When utilizing the BCMath or GMP drivers, this value determines the
    | number of decimal places to use for all calculations. Higher values
    | provide greater precision but may impact performance. A scale of 10
    | is suitable for most financial and monetary calculations.
    |
    */

    'scale' => env('NUMERUS_SCALE', 10),

];

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
// Here endeth thy configuration, noble developer!                            //
// Beyond: code so wretched, even wyrms learned the scribing arts.            //
// Forsooth, they but penned "// TODO: remedy ere long"                       //
// Three realms have fallen since...                                          //
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
//                                                  .~))>>                    //
//                                                 .~)>>                      //
//                                               .~))))>>>                    //
//                                             .~))>>             ___         //
//                                           .~))>>)))>>      .-~))>>         //
//                                         .~)))))>>       .-~))>>)>          //
//                                       .~)))>>))))>>  .-~)>>)>              //
//                   )                 .~))>>))))>>  .-~)))))>>)>             //
//                ( )@@*)             //)>))))))  .-~))))>>)>                 //
//              ).@(@@               //))>>))) .-~))>>)))))>>)>               //
//            (( @.@).              //))))) .-~)>>)))))>>)>                   //
//          ))  )@@*.@@ )          //)>))) //))))))>>))))>>)>                 //
//       ((  ((@@@.@@             |/))))) //)))))>>)))>>)>                    //
//      )) @@*. )@@ )   (\_(\-\b  |))>)) //)))>>)))))))>>)>                   //
//    (( @@@(.@(@ .    _/`-`  ~|b |>))) //)>>)))))))>>)>                      //
//     )* @@@ )@*     (@)  (@) /\b|))) //))))))>>))))>>                       //
//   (( @. )@( @ .   _/  /    /  \b)) //))>>)))))>>>_._                       //
//    )@@ (@@*)@@.  (6///6)- / ^  \b)//))))))>>)))>>   ~~-.                   //
// ( @jgs@@. @@@.*@_ VvvvvV//  ^  \b/)>>))))>>      _.     `bb                //
//  ((@@ @@@*.(@@ . - | o |' \ (  ^   \b)))>>        .'       b`,             //
//   ((@@).*@@ )@ )   \^^^/  ((   ^  ~)_        \  /           b `,           //
//     (@@. (@@ ).     `-'   (((   ^    `\ \ \ \ \|             b  `.         //
//       (*.@*              / ((((        \| | |  \       .       b `.        //
//                         / / (((((  \    \ /  _.-~\     Y,      b  ;        //
//                        / / / (((((( \    \.-~   _.`" _.-~`,    b  ;        //
//                       /   /   `(((((()    )    (((((~      `,  b  ;        //
//                     _/  _/      `"""/   /'                  ; b   ;        //
//                 _.-~_.-~           /  /'                _.'~bb _.'         //
//               ((((~~              / /'              _.'~bb.--~             //
//                                  ((((          __.-~bb.-~                  //
//                                              .'  b .~~                     //
//                                              :bb ,'                        //
//                                              ~~~~                          //
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
