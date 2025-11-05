import Preset from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';

const customThemePreset = definePreset(Preset, {
    semantic: {
        primary: {
            50: '#ADE8FF',
            100: '#99E2FF',
            200: '#85DCFF',
            300: '#70D7FF',
            400: '#5CD1FF',
            500: '#47CBFF',
            600: '#33C5FF',
            700: '#1FBFFF',
            800: '#0ABAFF',
            900: '#00AFF5',
            950: '#009DDD',
        },
        colorScheme: {
            light: {
                primary: {
                    color: '#009DDD',
                    inverseColor: '#ffffff',
                    hoverColor: '#0084B8',
                    activeColor: '#03256C',
                },
                highlight: {
                    background: '#00AFF5',
                    focusBackground: '#2541B2',
                    color: '#ffffff',
                    focusColor: '#ffffff',
                },
                surface: {
                    50: '#00AFF5',
                    100: '#fafafa',
                    200: '#ADE8FF',
                    300: '#ADE8FF',
                    400: '#009DDD',
                    500: '#009DDD',
                    600: '#009DDD',
                    700: '#22262A',
                    800: '#009DDD',
                    900: '#000000',
                    950: '#000000',
                },
            },
            dark: {
                primary: {
                    color: '#009DDD',
                    inverseColor: '#ffffff',
                    hoverColor: '#0084B8',
                    activeColor: '#03256C',
                },
                highlight: {
                    background: '#03256C',
                    focusBackground: '#2541B2',
                    color: '#ffffff',
                    focusColor: '#ffffff',
                },
                surface: {
                    50: '#F9FAFB',
                    100: '#FFFFFF',
                    200: '#F3F4F6',
                    300: '#22262A',
                    400: '#ffffff',
                    500: '#009DDD',
                    600: '#009DDD',
                    700: '#009DDD',
                    800: '#33C5FF',
                    900: '#22262A',
                    950: '#0f0f0f',
                },
            },
        },
    },
    components: {
        card: {
            colorScheme: {
                light: {
                    root: {
                        shadow: '0 1px 3px 0 #ADE8FF, 0 1px 2px -1px #ADE8FF'
                    }
                },
                dark: {
                    root: {
                        shadow: '0 1px 3px 0 #009DDD, 0 1px 2px -1px #009DDD'
                    }
                }
            }
        },
        datatable: {
            header: {
                borderWidth: '1px',
            },
            paginatorBottom: {
                borderWidth: '0 1px 1px 1px'
            },
        },
        progressspinner: {
            colorScheme: {
                light: {
                    root: {
                        colorOne: '#ADE8FF',
                        colorTwo: '#5CD1FF',
                        colorThree: '#1FBFFF',
                        colorFour: '#009DDD'
                    }
                },
                dark: {
                    root: {
                        colorOne: '#ADE8FF',
                        colorTwo: '#5CD1FF',
                        colorThree: '#1FBFFF',
                        colorFour: '#009DDD'
                    }
                }
            }
        }
    }
});

export default customThemePreset;
