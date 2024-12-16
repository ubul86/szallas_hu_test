import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { VDateInput } from 'vuetify/labs/VDateInput'
import DayJsAdapter from '@date-io/dayjs'

export default createVuetify({
    components: {
        ...components,
        VDateInput,
    },
    date: {
        adapter: DayJsAdapter,
    },
    directives,
    VDateInput
});
